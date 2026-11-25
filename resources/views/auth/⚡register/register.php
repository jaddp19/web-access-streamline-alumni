<?php

use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $first_name = '';

    public string $middle_name = '';

    public string $last_name = '';

    public string $school_id = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /** Honeypot — real users never see or fill this. */
    public string $website = '';

    public string $formError = '';

    /** Max registration attempts per window (successful or not). */
    protected const MAX_ATTEMPTS = 5;

    /** Window in seconds (1 hour). */
    protected const WINDOW_SECONDS = 3600;

    /**
     * NOTE: there is intentionally NO role / is_active property here.
     * Public Livewire properties can be tampered with from the browser,
     * so the role and active flag are set server-side only.
     */

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100', "regex:/^[\pL\s.'-]+$/u"],
            'middle_name' => ['nullable', 'string', 'max:100', "regex:/^[\pL\s.'-]+$/u"],
            'last_name' => ['required', 'string', 'max:100', "regex:/^[\pL\s.'-]+$/u"],
            // Adjust the pattern if your school IDs follow another format.
            'school_id' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            // Remove ",dns" if your local/dev environment has no DNS access.
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'password' => [
                'required', 'string', 'confirmed',
                Password::min(12)->letters()->mixedCase()->numbers()->symbols()->uncompromised(),
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.regex' => 'First name contains invalid characters.',
            'middle_name.regex' => 'Middle name contains invalid characters.',
            'last_name.required' => 'Last name is required.',
            'last_name.regex' => 'Last name contains invalid characters.',
            'school_id.required' => 'School ID number is required.',
            'school_id.regex' => 'School ID is not valid.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    public function register()
    {
        $this->formError = '';

        // Honeypot tripped → pretend nothing happened, create nothing.
        if ($this->website !== '') {
            Log::warning('Registration honeypot triggered', ['ip' => request()->ip()]);
            $this->formError = 'We could not complete your registration. Please try again later.';

            return;
        }

        // Throttle per IP, counting every attempt (not just failures),
        // so mass account creation and ID guessing are both slowed down.
        $key = 'register:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);
            $this->formError = "Too many registration attempts. Please try again in {$minutes} minute(s).";

            return;
        }

        RateLimiter::hit($key, self::WINDOW_SECONDS);

        // Normalise before validating.
        $this->first_name = trim(preg_replace('/\s+/', ' ', $this->first_name));
        $this->middle_name = trim(preg_replace('/\s+/', ' ', $this->middle_name));
        $this->last_name = trim(preg_replace('/\s+/', ' ', $this->last_name));
        $this->school_id = trim($this->school_id);
        $this->email = Str::lower(trim($this->email));

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->reset('password', 'password_confirmation');

            throw $e;
        }

        // Duplicate check done manually (not via the `unique` rule) so the
        // response is the SAME whether the email or the school ID exists —
        // this prevents account enumeration.
        $exists = User::where('email', $this->email)
            ->orWhere('school_id', $this->school_id)
            ->exists();

        if ($exists) {
            return $this->failGeneric('duplicate');
        }

        try {
            $user = DB::transaction(function () {
                $user = User::create([
                    'first_name' => $this->first_name,
                    'middle_name' => $this->middle_name !== '' ? $this->middle_name : null,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'school_id' => $this->school_id,
                    'password' => $this->password, // hashed by the model cast — do NOT Hash::make here
                    'is_active' => true,
                ]);

                // Role is always forced to alumni, server-side.
                // If the role doesn't exist this throws and the transaction rolls back.
                $user->assignRole('alumni');

                // Avoid the "created_at == updated_at" first-login check in
                // needsForcedPasswordChange(): they just chose their own password.
                $user->updated_at = $user->created_at->copy()->addSecond();
                $user->saveQuietly();

                return $user;
            });
        } catch (QueryException $e) {
            // Race condition: unique index caught a duplicate between the check and the insert.
            return $this->failGeneric('race_duplicate');
        } catch (\Throwable $e) {
            report($e);
            $this->reset('password', 'password_confirmation');
            $this->formError = 'Something went wrong while creating your account. Please try again later.';

            return;
        }

        Log::info('Alumni self-registration', [
            'user_id' => $user->id,
            'ip' => request()->ip(),
        ]);

        RateLimiter::clear($key);

        Auth::login($user);
        request()->session()->regenerate();

        // New alumni have no tracer study yet → send them to the form.
        return redirect()->route('form');
    }

    protected function failGeneric(string $reason)
    {
        Log::warning('Registration rejected', [
            'reason' => $reason,
            'email' => $this->email,
            'ip' => request()->ip(),
        ]);

        $this->reset('password', 'password_confirmation');
        $this->formError = 'We could not complete your registration with the details provided. '
            .'If you already have an account, sign in or use “Forgot password”, or contact the alumni coordinator.';
    }

    // =========================================================
    //  COMPUTED STATS (same cache keys as the login page)
    // =========================================================

    #[Computed]
    public function alumni(): int
    {
        return Cache::remember('login:stats', now()->addMinutes(10), fn () => User::role('alumni')->count());
    }

    #[Computed]
    public function courses(): int
    {
        return Cache::remember('login:stats:courses', now()->addMinutes(10), fn () => Course::count());
    }

    #[Computed]
    public function departments(): int
    {
        return Cache::remember('login:stats:departments', now()->addMinutes(10), fn () => Department::count());
    }
};