<?php

use App\Exceptions\OtpCooldownException;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    // ============ FORGOT PASSWORD STATE ============
    public bool $showForgotModal = false;

    public int $forgotStep = 1;   // 1 = email, 2 = school ID, 3 = verification code

    public string $forgotEmail = '';

    public string $forgotSchoolId = '';

    public string $forgotCode = '';

    public int $forgotOtpSentAt = 0;

    public string $forgotStatusMessage = '';

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
            'email' => ['required', 'email:rfc', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
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
                'email' => $this->email,
                'ip' => request()->ip(),
                'reason' => $user ? 'wrong_password' : 'unknown_email',
            ]);

            $this->addError('email', 'Invalid credentials.');

            return;
        }

        // ── Account deactivation gate ──────────────────────────────────
        // Checked AFTER credential verification so a deactivated user's
        // password can't be probed via a different error message. The
        // email/password match must be proven first.
        if (! $user->is_active) {
            RateLimiter::hit($key, self::LOCKOUT_SECONDS);

            Log::notice('Deactivated account login attempt', [
                'user_id' => $user->id,
                'email' => $this->email,
                'ip' => request()->ip(),
            ]);

            $this->addError('email', 'This account has been deactivated. Please contact the alumni coordinator.');

            return;
        }
        // ────────────────────────────────────────────────────────────────

        Auth::login($user, $this->remember);
        session()->regenerate();
        RateLimiter::clear($key);

        return $this->redirectBasedOnRole($user);
    }

    protected function redirectBasedOnRole(User $user)
    {
        // One DB hit for all roles at once — Spatie caches internally,
        // but this avoids repeated `hasRole()` lookups.
        $roles = $user->getRoleNames();

        if ($roles->contains('alumni coordinator')) {
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
        session()->invalidate();
        session()->regenerateToken();

        $this->addError('email', 'This account has no assigned role. Please contact the alumni coordinator.');
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->email).'|'.request()->ip()
        );
    }

    // =========================================================
    //  FORGOT PASSWORD — two-step verification
    // =========================================================

    public function openForgotModal(): void
    {
        $this->reset(
            'forgotStep',
            'forgotEmail',
            'forgotSchoolId',
            'forgotCode',
            'forgotOtpSentAt',
            'forgotStatusMessage',
        );
        $this->forgotStep = 1;
        session()->forget('forgot.verified_email');
        $this->resetErrorBag();
        $this->showForgotModal = true;
    }

    public function closeForgotModal(): void
    {
        $this->showForgotModal = false;
        $this->forgotStep = 1;
        $this->forgotEmail = '';
        $this->forgotSchoolId = '';
        $this->forgotCode = '';
        $this->forgotOtpSentAt = 0;
        $this->forgotStatusMessage = '';
        session()->forget('forgot.verified_email');
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
            'forgotEmail.email' => 'Please enter a valid email address.',
        ]);

        $this->forgotEmail = Str::lower(trim($this->forgotEmail));
        session()->forget('forgot.verified_email');
        $this->forgotCode = '';
        $this->forgotOtpSentAt = 0;
        $this->forgotStatusMessage = '';
        $this->resetErrorBag();
        $this->forgotStep = 2;
    }

    /**
     * Step 2 — verifies the school ID, then emails a 6-digit code.
     * Uses the same generic error for "email not found" and
     * "school ID mismatch" so the failure path leaks nothing.
     */
    public function verifyForgotSchoolIdAndSendCode(): void
    {
        // Rate-limit per email + IP so an attacker can't brute-force
        // school IDs. School IDs are somewhat predictable (0001-0001,
        // 0001-0002, ...), so this matters.
        $key = 'forgot:'.Str::transliterate($this->forgotEmail).'|'.request()->ip();

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
                'email' => $this->forgotEmail,
                'ip' => request()->ip(),
                'reason' => $user ? 'school_id_mismatch' : 'unknown_email',
            ]);

            $this->addError('forgotSchoolId', 'The information provided does not match our records.');

            return;
        }

        RateLimiter::clear($key);

        try {
            app(OtpService::class)->send(
                $user->email,
                OtpService::PURPOSE_RESET,
                ['name' => $user->name],
            );
        } catch (OtpCooldownException $e) {
            $this->addError('forgotSchoolId', "Please wait {$e->retryAfter} second(s) before requesting another code.");

            return;
        } catch (\Throwable $e) {
            report($e);
            $this->addError('forgotSchoolId', 'Could not send the verification code right now. Please try again later.');

            return;
        }

        // The code is tied to this verified address, not the public
        // forgotEmail property, so step 3 cannot be pointed at another inbox.
        session()->put('forgot.verified_email', $user->email);

        $this->forgotEmail = $user->email;
        $this->forgotCode = '';
        $this->forgotOtpSentAt = now()->timestamp;
        $this->forgotStatusMessage = 'We sent a 6-digit code to '.$user->email.'. It expires in 10 minutes.';
        $this->forgotStep = 3;
        $this->resetErrorBag();

        Log::info('Password reset code requested', [
            'user_id' => $user->id,
            'ip' => request()->ip(),
        ]);
    }

    /**
     * Step 3 — checks the emailed code, then starts the password reset.
     */
    public function verifyForgotCode()
    {
        $email = session()->get('forgot.verified_email');

        if (! is_string($email) || $email === '') {
            $this->forgotStep = 1;
            $this->addError('forgotEmail', 'Your verification session expired. Please start again.');

            return;
        }

        $this->validate([
            'forgotCode' => ['required', 'digits:6'],
        ], [
            'forgotCode.required' => 'Please enter the 6-digit code we emailed you.',
            'forgotCode.digits' => 'The verification code must be exactly 6 digits.',
        ]);

        $user = User::where('email', $email)->first();

        if (! $user) {
            session()->forget('forgot.verified_email');
            $this->forgotStep = 1;
            $this->addError('forgotEmail', 'We could not complete the reset. Please start again.');

            return;
        }

        $status = app(OtpService::class)->verify($email, OtpService::PURPOSE_RESET, $this->forgotCode);

        if ($status !== OtpService::VERIFY_OK) {
            $this->addError('forgotCode', OtpService::errorMessage($status));

            return;
        }

        try {
            $token = Password::broker()->createToken($user);
        } catch (\Throwable $e) {
            report($e);
            $this->forgotOtpSentAt = 0;
            $this->addError('forgotCode', 'We could not start the password reset right now. Please request a new code and try again.');

            return;
        }

        session()->forget('forgot.verified_email');

        Log::info('Password reset verified by email code', [
            'user_id' => $user->id,
            'ip' => request()->ip(),
        ]);

        session()->flash('success', 'Email verified. Please choose a new password.');

        return redirect()->route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);
    }

    public function resendForgotCode(): void
    {
        $email = session()->get('forgot.verified_email');

        if (! is_string($email) || $email === '') {
            $this->forgotStep = 1;
            $this->addError('forgotEmail', 'Your verification session expired. Please start again.');

            return;
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            session()->forget('forgot.verified_email');
            $this->forgotStep = 1;
            $this->addError('forgotEmail', 'We could not complete the reset. Please start again.');

            return;
        }

        try {
            app(OtpService::class)->send(
                $email,
                OtpService::PURPOSE_RESET,
                ['name' => $user->name],
            );
        } catch (OtpCooldownException $e) {
            $this->addError('forgotCode', "Please wait {$e->retryAfter} second(s) before requesting another code.");

            return;
        } catch (\Throwable $e) {
            report($e);
            $this->addError('forgotCode', 'Could not resend the verification code right now. Please try again later.');

            return;
        }

        $this->forgotOtpSentAt = now()->timestamp;
        $this->forgotCode = '';
        $this->forgotStatusMessage = 'A new 6-digit code has been sent to '.$email.'.';
        $this->resetErrorBag('forgotCode');
    }

    public function backToForgotEmail(): void
    {
        $this->forgotStep = 1;
        $this->forgotCode = '';
        $this->forgotOtpSentAt = 0;
        $this->forgotStatusMessage = '';
        session()->forget('forgot.verified_email');
        $this->resetErrorBag();
    }

    public function backToForgotSchoolId(): void
    {
        $this->forgotStep = 2;
        $this->forgotCode = '';
        $this->forgotOtpSentAt = 0;
        $this->forgotStatusMessage = '';
        $this->resetErrorBag();
    }

    public function forgotResendRemaining(): int
    {
        if ($this->forgotOtpSentAt <= 0) {
            return 0;
        }

        return max(
            0,
            OtpService::RESEND_COOLDOWN_SECONDS - (now()->timestamp - $this->forgotOtpSentAt)
        );
    }

};
