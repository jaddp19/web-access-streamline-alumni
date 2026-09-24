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

    /** Max failed attempts before lockout. */
    protected const MAX_ATTEMPTS = 5;

    /** Lockout window in seconds. */
    protected const LOCKOUT_SECONDS = 60;

    // =========================================================
    //  LOGIN VALIDATION
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

        $user = User::where('email', Str::lower($this->email))
            ->with('roles:id,name')
            ->first();

        if (! $user) {
            RateLimiter::hit($key, self::LOCKOUT_SECONDS);
            $this->addError('email', 'This email is not registered.');
            return;
        }

        if (! Hash::check($this->password, $user->password)) {
            RateLimiter::hit($key, self::LOCKOUT_SECONDS);

            Log::warning('Failed login attempt', [
                'email' => $this->email,
                'ip'    => request()->ip(),
            ]);

            $this->addError('password', 'Wrong password.');
            return;
        }

        Auth::login($user, $this->remember);
        request()->session()->regenerate();
        RateLimiter::clear($key);

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

    /** Step 1 — verify the email exists, then ask for the school ID. */
    public function verifyForgotEmail(): void
    {
        $this->validate([
            'forgotEmail' => ['required', 'email:rfc', 'max:255'],
        ], [
            'forgotEmail.required' => 'Please enter your email address.',
            'forgotEmail.email'    => 'Please enter a valid email address.',
        ]);

        $exists = User::where('email', Str::lower($this->forgotEmail))->exists();

        if (! $exists) {
            $this->addError('forgotEmail', 'This email is not registered.');
            return;
        }

        $this->resetErrorBag();
        $this->forgotStep = 2;
    }

    /** Step 2 — verify the school ID, then send the reset link. */
    public function verifyForgotSchoolIdAndSend(): void
    {
        $this->validate([
            'forgotSchoolId' => ['required', 'string', 'max:50'],
        ], [
            'forgotSchoolId.required' => 'Please enter your school ID number.',
        ]);

        $user = User::where('email', Str::lower($this->forgotEmail))->first();

        if (! $user) {
            $this->forgotStep = 1;
            $this->addError('forgotEmail', 'This email is not registered.');
            return;
        }

        $matches = Str::lower(trim($user->school_id)) === Str::lower(trim($this->forgotSchoolId));

        if (! $matches) {
            $this->addError('forgotSchoolId', 'That school ID does not match our records for this email.');
            return;
        }

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
                'email'   => $user->email,
                'ip'      => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);
            $this->addError('forgotSchoolId', 'Could not send the reset email right now. Please try again later.');
            return;
        }

        $this->forgotSuccessMessage = "If that information matches our records, we've sent a password reset link to {$user->email}. Check your inbox (and spam folder).";
        $this->forgotStep           = 3;
    }

    // =========================================================
    //  COMPUTED
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