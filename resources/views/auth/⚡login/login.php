<?php

use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
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

    // ============ FORGOT PASSWORD STATE ============
    public bool $showForgotModal = false;
    public int $forgotStep = 1;   // 1 = email, 2 = school ID, 3 = success
    public string $forgotEmail = '';
    public string $forgotSchoolId = '';
    public string $forgotSuccessMessage = '';

    /** Max failed login attempts before lockout. */
    protected const MAX_ATTEMPTS = 5;

    /** Login lockout window in seconds. */
    protected const LOCKOUT_SECONDS = 60;

    /** Max failed forgot-password attempts before lockout. */
    protected const FORGOT_MAX_ATTEMPTS = 5;

    /** Forgot-password lockout window in seconds (5 minutes). */
    protected const FORGOT_LOCKOUT_SECONDS = 300;

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

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            $this->addError(
                'email',
                "Too many login attempts. Please try again in {$seconds} seconds."
            );
            return;
        }

        $user = User::where('email', Str::lower($this->email))->first();

        // Combined check — the SAME error is shown whether the email
        // exists or the password is wrong, so attackers can't tell.
        if (! $user || ! Hash::check($this->password, $user->password)) {
            RateLimiter::hit($key, self::LOCKOUT_SECONDS);

            Log::warning('Failed login attempt', [
                'email'  => $this->email,
                'ip'     => request()->ip(),
                'reason' => $user ? 'wrong_password' : 'unknown_email',
            ]);

            $this->addError('email', 'Invalid credentials.');
            return;
        }

        Auth::login($user, $this->remember);
        request()->session()->regenerate();
        RateLimiter::clear($key);

        return $this->redirectBasedOnRole($user);
    }

    protected function redirectBasedOnRole(User $user)
    {
        // One DB hit for all roles at once — Spatie caches internally,
        // but this avoids repeated `hasRole()` lookups.
        $roles = $user->getRoleNames();

        if ($roles->contains('registrar')) {
            return redirect()->route('super-admin.dashboard');
        }

        if ($roles->contains('program head')) {
            return redirect()->route('admin.dashboard');
        }

        if ($roles->contains('alumni')) {
            $hasTracer = $user->tracerStudy()->exists();

            return redirect()->route($hasTracer ? 'alumni.dashboard' : 'form');
        }

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->addError('email', 'This account has no assigned role. Please contact the registrar.');
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->email) . '|' . request()->ip()
        );
    }

    // =========================================================
    //  FORGOT PASSWORD — two-step verification
    // =========================================================

    public function openForgotModal(): void
    {
        $this->reset('forgotStep', 'forgotEmail', 'forgotSchoolId', 'forgotSuccessMessage');
        $this->forgotStep      = 1;
        $this->resetErrorBag();
        $this->showForgotModal = true;
    }

    public function closeForgotModal(): void
    {
        $this->showForgotModal      = false;
        $this->forgotStep           = 1;
        $this->forgotEmail          = '';
        $this->forgotSchoolId       = '';
        $this->forgotSuccessMessage = '';
        $this->resetErrorBag();
    }

    /**
     * Step 1 — always advances to step 2, regardless of whether the email
     * exists. Prevents user enumeration via the "email not registered"
     * message.
     */
    public function verifyForgotEmail(): void
    {
        $this->validate([
            'forgotEmail' => ['required', 'email:rfc', 'max:255'],
        ], [
            'forgotEmail.required' => 'Please enter your email address.',
            'forgotEmail.email'    => 'Please enter a valid email address.',
        ]);

        $this->forgotEmail = Str::lower(trim($this->forgotEmail));
        $this->resetErrorBag();
        $this->forgotStep = 2;
    }

    /**
     * Step 2 — verifies the school ID, then sends the reset link.
     * Uses the same generic error for "email not found" and
     * "school ID mismatch" so the failure path leaks nothing.
     */
    public function verifyForgotSchoolIdAndSend(): void
    {
        // Rate-limit per email + IP so an attacker can't brute-force
        // school IDs. School IDs are somewhat predictable (0001-0001,
        // 0001-0002, ...), so this matters.
        $key = 'forgot:' . Str::transliterate($this->forgotEmail) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, self::FORGOT_MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);
            $this->addError('forgotSchoolId', "Too many attempts. Please try again in {$minutes} minute(s).");
            return;
        }

        $this->validate([
            'forgotSchoolId' => ['required', 'string', 'max:50'],
        ], [
            'forgotSchoolId.required' => 'Please enter your school ID number.',
        ]);

        $user = User::where('email', Str::lower($this->forgotEmail))->first();

        // Combined, constant-time comparison. Same error for both branches.
        $matches = $user && hash_equals(
            Str::lower(trim((string) $user->school_id)),
            Str::lower(trim($this->forgotSchoolId))
        );

        if (! $matches) {
            RateLimiter::hit($key, self::FORGOT_LOCKOUT_SECONDS);

            Log::warning('Forgot-password verification failed', [
                'email'  => $this->forgotEmail,
                'ip'     => request()->ip(),
                'reason' => $user ? 'school_id_mismatch' : 'unknown_email',
            ]);

            $this->addError('forgotSchoolId', 'The information provided does not match our records.');
            return;
        }

        RateLimiter::clear($key);

        try {
            $token = Password::broker()->createToken($user);

            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ]);

            EmailTemplateService::send('password-reset', $user->email, [
                'name'       => $user->name,
                'reset_url'  => $resetUrl,
                'expires_in' => (string) config('auth.passwords.users.expire', 60),
            ]);

            Log::info('Password reset requested', [
                'user_id' => $user->id,
                'ip'      => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);
            $this->addError('forgotSchoolId', 'Could not send the reset email right now. Please try again later.');
            return;
        }

        // Generic success message — doesn't echo back the email,
        // so it can't be used to confirm an address either.
        $this->forgotSuccessMessage = 'If that information matches our records, a password reset link has been sent. Check your inbox and spam folder.';
        $this->forgotStep = 3;
    }

    // =========================================================
    //  COMPUTED STATS
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