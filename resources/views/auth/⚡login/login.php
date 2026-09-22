<?php

use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    /** Max failed attempts before lockout. */
    protected const MAX_ATTEMPTS = 5;

    /** Lockout window in seconds. */
    protected const LOCKOUT_SECONDS = 60;

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'email'    => ['required', 'email:rfc', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required'    => 'Email is required.',
            'email.email'       => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.min'      => 'Password must be at least 6 characters.',
        ];
    }

    // =========================================================
    //  LOGIN
    // =========================================================

    public function login()
    {
        $this->validate();

        $key = $this->throttleKey();

        // ----- Rate limit check -----
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            $this->addError(
                'email',
                "Too many login attempts. Please try again in {$seconds} seconds."
            );
            return;
        }

        // ----- Find user -----
        $user = User::where('email', Str::lower($this->email))
            ->with('roles:id,name')
            ->first();

        if (! $user) {
            RateLimiter::hit($key, self::LOCKOUT_SECONDS);
            $this->addError('email', 'This email is not registered.');
            return;
        }

        // ----- Verify password (single check, no double-query) -----
        if (! Hash::check($this->password, $user->password)) {
            RateLimiter::hit($key, self::LOCKOUT_SECONDS);

            Log::warning('Failed login attempt', [
                'email' => $this->email,
                'ip'    => request()->ip(),
            ]);

            $this->addError('password', 'Wrong password.');
            return;
        }

        // ----- Log the user in (no second Hash check) -----
        Auth::login($user, $this->remember);
        request()->session()->regenerate();
        RateLimiter::clear($key);

        // ----- Role-based redirect -----
        return $this->redirectBasedOnRole($user);
    }

    protected function redirectBasedOnRole(User $user)
    {
        if ($user->hasRole('registrar')) {
            return redirect()->route('super-admin.dashboard');
        }

        if ($user->hasRole('program head')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('alumni')) {
            // Has the alumni started their tracer study yet?
            $hasTracer = $user->tracerStudy()->exists();

            return redirect()->route(
                $hasTracer ? 'alumni.dashboard' : 'form'
            );
        }

        // No recognized role → log out immediately
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->addError('email', 'This account has no assigned role. Please contact the registrar.');
    }

    /**
     * Unique key for this email + IP combo.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->email) . '|' . request()->ip()
        );
    }

    // =========================================================
    //  COMPUTED (cached — this page loads these every request)
    // =========================================================

    #[Computed]
    public function alumni(): int
    {
        return Cache::remember('login:stats', now()->addMinutes(10), function () {
            return User::role('alumni')->count();
        });
    }

    #[Computed]
    public function courses(): int
    {
        return Cache::remember('login:stats:courses', now()->addMinutes(10), function () {
            return Course::count();
        });
    }

    #[Computed]
    public function departments(): int
    {
        return Cache::remember('login:stats:departments', now()->addMinutes(10), function () {
            return Department::count();
        });
    }
};