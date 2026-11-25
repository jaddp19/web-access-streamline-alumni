<?php

use App\Models\Batch;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create Alumni Account')] #[Layout('layouts.auth')] class extends Component
{
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $school_id = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;

    /** Bot trap — must stay empty. */
    public string $website = '';

    #[Locked]
    public string $formToken = '';

    /** Failed registration attempts allowed before a temporary lockout. */
    protected const MAX_FAILED_ATTEMPTS = 10;

    /** Lockout window in seconds (1 hour). */
    protected const FAILED_LOCKOUT_SECONDS = 3600;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectBasedOnRole();

            return;
        }

        $this->formToken = Str::random(40);

        session()->put(
            'register.form.'.$this->formToken,
            now()->timestamp
        );
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\p{M}\s.\'-]+$/u',
            ],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\p{M}\s.\'-]+$/u',
            ],

            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'school_id' => [
                'required',
                'string',
                'max:30',
                'regex:/^\d{4}-\d{4}$/',
                Rule::unique('users', 'school_id'),
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! preg_match('/^(\d{4})-\d{4}$/', $value, $matches)) {
                        return;
                    }

                    if (! in_array($matches[1], $this->batchYears, true)) {
                        $fail('The school ID you entered does not match our records.');
                    }
                },
            ],

            'password' => [
                'required',
                'string',
                'min:12',
                'max:72',
                'confirmed',
                'different:email',
                Password::min(12)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (in_array($value, User::DEFAULT_PASSWORDS, true)) {
                        $fail('That password is reserved. Please choose a different one.');
                    }
                },
            ],
            'password_confirmation' => ['required', 'string'],

            'terms' => ['accepted'],
        ];
    }

    protected function messages(): array
    {
        return [
            'first_name.required' => 'Please enter your first name.',
            'first_name.min' => 'Your first name must be at least 2 characters.',
            'first_name.regex' => 'Your first name may only contain letters, spaces, periods, apostrophes, and hyphens.',

            'middle_name.max' => 'Your middle name may not be longer than 255 characters.',

            'last_name.required' => 'Please enter your last name.',
            'last_name.min' => 'Your last name must be at least 2 characters.',
            'last_name.regex' => 'Your last name may only contain letters, spaces, periods, apostrophes, and hyphens.',

            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Your email address may not be longer than 255 characters.',
            'email.unique' => 'That email address is already registered. Please sign in or reset your password.',

            'school_id.required' => 'Please enter your school ID.',
            'school_id.string' => 'Please enter a valid school ID.',
            'school_id.max' => 'The school ID you entered is too long.',
            'school_id.regex' => 'Please enter your full school ID exactly as it appears on your alumni records.',
            'school_id.unique' => 'This school ID is already registered. If it is yours, please sign in or reset your password.',

            'password.required' => 'Please choose a password.',
            'password.min' => 'Your password must be at least 12 characters.',
            'password.max' => 'Your password may not be longer than 72 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.different' => 'Your password must be different from your email address.',
            'password.mixed' => 'Your password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Your password must contain at least one number.',
            'password.symbols' => 'Your password must contain at least one symbol (e.g. ! @ # $ %).',
            'password.uncompromised' => 'This password has appeared in a data breach. Please choose a different one.',

            'password_confirmation.required' => 'Please confirm your password.',

            'terms.accepted' => 'You must agree to the Terms and Conditions and Privacy Policy.',
        ];
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function batchYears(): array
    {
        return Cache::remember('batches:years-list', now()->addMinutes(10), function () {
            return Batch::query()
                ->orderByDesc('batch_name')
                ->pluck('batch_name')
                ->map(fn ($year) => (string) $year)
                ->all();
        });
    }

    // =========================================================
    //  REGISTER
    // =========================================================

    public function register()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        $monthlyKey = $this->monthlyRegistrationKey();
        $failureKey = $this->failedRegistrationKey();

        // One successful registration per connection per month.
        if (Cache::has($monthlyKey)) {
            Log::warning('Registration blocked: monthly limit', [
                'ip' => request()->ip(),
            ]);

            $this->addError(
                'form',
                'You have already registered from this connection. Only one registration is allowed per month — please sign in instead.'
            );

            return;
        }

        // Repeated failed/spam attempts trigger a one-hour lockout.
        if (RateLimiter::tooManyAttempts($failureKey, self::MAX_FAILED_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($failureKey) / 60);

            Log::warning('Registration blocked: failure lockout', [
                'ip' => request()->ip(),
            ]);

            $this->addError(
                'form',
                "Too many failed registration attempts. Please try again in {$minutes} minute(s)."
            );

            return;
        }

        // ── Bot / timing checks ──────────────────────────────────────
        $openedAt = (int) session()->get('register.form.'.$this->formToken, 0);
        $elapsed = $openedAt > 0 ? now()->timestamp - $openedAt : 0;
        $honeypotTripped = trim($this->website) !== '';

        if ($honeypotTripped || $openedAt === 0 || $elapsed < 3) {
            RateLimiter::hit($failureKey, self::FAILED_LOCKOUT_SECONDS);

            Log::warning('Registration blocked', [
                'ip' => request()->ip(),
                'reason' => $honeypotTripped ? 'honeypot' : 'timing',
            ]);

            $this->addError('form', 'We could not create your account. Please refresh the page and try again.');

            return;
        }

        if ($elapsed > 3600) {
            session()->forget('register.form.'.$this->formToken);

            $this->addError('form', 'Your form session expired. Please refresh the page and try again.');

            return;
        }
        // ─────────────────────────────────────────────────────────────

        // Normalize before validating so uniqueness checks are consistent.
        $this->first_name = $this->cleanName($this->first_name);
        $this->middle_name = $this->cleanName($this->middle_name);
        $this->last_name = $this->cleanName($this->last_name);
        $this->email = Str::lower(trim($this->email));
        $this->school_id = $this->normalizeSchoolId($this->school_id);

        try {
            $validated = $this->validate();
        } catch (ValidationException $e) {
            // Never keep the plain password in the Livewire snapshot after
            // a failed submission — make the user type it again.
            $this->password = '';
            $this->password_confirmation = '';

            // Every failed submission counts toward the one-hour lockout.
            RateLimiter::hit($failureKey, self::FAILED_LOCKOUT_SECONDS);

            throw $e;
        }

        try {
            $user = DB::transaction(function () use ($validated) {
                $user = User::create([
                    'first_name' => $validated['first_name'],
                    'middle_name' => filled($validated['middle_name'] ?? null)
                        ? $validated['middle_name']
                        : null,
                    'last_name' => $validated['last_name'],
                    // `name` is composed automatically by the User model.
                    'email' => $validated['email'],
                    'school_id' => $validated['school_id'],
                    'password' => Hash::make($validated['password']),
                    'is_active' => true,
                ]);

                // Role is assigned server-side only. There is no role field
                // in the form, so it cannot be tampered with.
                $user->assignRole('alumni');

                // The user just chose this password, so don't let the
                // first-login password-change middleware fire for it.
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['updated_at' => now()->addSecond()]);

                return $user;
            });
        } catch (QueryException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                RateLimiter::hit($failureKey, self::FAILED_LOCKOUT_SECONDS);

                $this->addError(
                    'form',
                    'An account with that email or school ID already exists. Please sign in or reset your password.'
                );

                return;
            }

            RateLimiter::hit($failureKey, self::FAILED_LOCKOUT_SECONDS);
            report($e);
            $this->addError('form', 'We could not create your account right now. Please try again.');

            return;
        } catch (\Throwable $e) {
            report($e);
            $this->addError('form', 'We could not create your account right now. Please try again.');

            return;
        }

        session()->forget('register.form.'.$this->formToken);
        RateLimiter::clear($failureKey);

        // Block this connection from creating another account for a month.
        Cache::put($monthlyKey, true, now()->addMonth());

        Auth::login($user);
        request()->session()->regenerate();

        Log::info('Alumni self-registration', [
            'user_id' => $user->id,
            'ip' => request()->ip(),
        ]);

        return redirect()->route('form');
    }

    protected function cleanName(string $value): string
    {
        $cleaned = preg_replace('/\s+/u', ' ', strip_tags($value));

        return trim($cleaned ?? '');
    }

    protected function normalizeSchoolId(string $value): string
    {
        $value = trim($value);
        $value = str_replace(['–', '—'], '-', $value);
        $value = preg_replace('/\s+/', '', $value) ?? '';

        // Accept 20201234 as well as 2020-1234.
        if (preg_match('/^\d{8}$/', $value)) {
            $value = substr($value, 0, 4).'-'.substr($value, 4);
        }

        return $value;
    }

    protected function monthlyRegistrationKey(): string
    {
        return 'register:monthly:'.sha1((string) request()->ip());
    }

    protected function failedRegistrationKey(): string
    {
        return 'register:failures:'.sha1((string) request()->ip());
    }


    protected function redirectBasedOnRole()
    {
        $roles = Auth::user()->getRoleNames();

        if ($roles->contains('alumni coordinator')) {
            return redirect()->route('super-admin.dashboard');
        }

        if ($roles->contains('program head')) {
            return redirect()->route('admin.dashboard');
        }

        if ($roles->contains('alumni')) {
            return redirect()->route(
                Auth::user()->tracerStudy()->exists() ? 'alumni.dashboard' : 'form'
            );
        }

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
};