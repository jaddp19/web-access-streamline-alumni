<?php

namespace App\Services;

use App\Exceptions\OtpCooldownException;
use App\Models\EmailVerificationCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OtpService
{
    public const PURPOSE_REGISTER = 'register';

    public const PURPOSE_RESET = 'reset';

    public const CODE_LENGTH = 6;

    /** How long a code stays valid. */
    public const EXPIRY_MINUTES = 10;

    /** Wrong-code attempts allowed before the code is destroyed. */
    public const MAX_ATTEMPTS = 5;

    /** Seconds before the user may request another code. */
    public const RESEND_COOLDOWN_SECONDS = 60;

    /** Hard cap on codes sent per email + purpose + IP per hour. */
    public const MAX_SENDS_PER_HOUR = 5;

    public const VERIFY_OK = 'ok';

    public const VERIFY_INVALID = 'invalid';

    public const VERIFY_EXPIRED = 'expired';

    public const VERIFY_MISSING = 'missing';

    public const VERIFY_LOCKED = 'locked';

    /**
     * Create a new code, replace any previous one, and email it immediately.
     *
     * @throws OtpCooldownException when the resend cooldown or hourly cap is hit
     */
    public function send(string $email, string $purpose, array $placeholders = []): void
    {
        $email = $this->normalizeEmail($email);
        $this->assertPurpose($purpose);

        $record = EmailVerificationCode::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->first();

        if ($record && $record->last_sent_at) {
            $retryAfter = $record->last_sent_at
                ->copy()
                ->addSeconds(self::RESEND_COOLDOWN_SECONDS)
                ->getTimestamp() - now()->getTimestamp();

            if ($retryAfter > 0) {
                throw new OtpCooldownException($retryAfter);
            }
        }

        $rateKey = $this->sendRateKey($email, $purpose);

        if (RateLimiter::tooManyAttempts($rateKey, self::MAX_SENDS_PER_HOUR)) {
            throw new OtpCooldownException(max(1, RateLimiter::availableIn($rateKey)));
        }

        $code = $this->generateCode();

        $record = EmailVerificationCode::updateOrCreate(
            ['email' => $email, 'purpose' => $purpose],
            [
                'code_hash' => Hash::make($code),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
                'last_sent_at' => now(),
            ]
        );

        RateLimiter::hit($rateKey, 3600);

        try {
            EmailTemplateService::sendNow($this->templateSlug($purpose), $email, array_merge(
                $placeholders,
                [
                    'code' => $code,
                    'expires_in' => self::EXPIRY_MINUTES.' minutes',
                ]
            ));
        } catch (\Throwable $e) {
            // Never leave a code behind that the user never received.
            $record->delete();

            throw $e;
        }
    }

    /**
     * Check a submitted code.
     *
     * Returns one of the VERIFY_* constants. A successful check consumes the
     * code so it cannot be replayed.
     */
    public function verify(string $email, string $purpose, string $code): string
    {
        $email = $this->normalizeEmail($email);
        $this->assertPurpose($purpose);

        $record = EmailVerificationCode::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->first();

        if (! $record) {
            return self::VERIFY_MISSING;
        }

        if ($record->expires_at->isPast()) {
            $record->delete();

            return self::VERIFY_EXPIRED;
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            $record->delete();

            return self::VERIFY_LOCKED;
        }

        if (! Hash::check(trim($code), $record->code_hash)) {
            $record->forceFill(['attempts' => $record->attempts + 1])->save();

            if ($record->attempts >= self::MAX_ATTEMPTS) {
                $record->delete();

                return self::VERIFY_LOCKED;
            }

            return self::VERIFY_INVALID;
        }

        $record->delete();

        return self::VERIFY_OK;
    }

    /**
     * Seconds left before another code may be requested. Zero means now.
     */
    public function secondsUntilResend(string $email, string $purpose): int
    {
        $record = EmailVerificationCode::query()
            ->where('email', $this->normalizeEmail($email))
            ->where('purpose', $purpose)
            ->first();

        if (! $record || ! $record->last_sent_at) {
            return 0;
        }

        return max(
            0,
            $record->last_sent_at
                ->copy()
                ->addSeconds(self::RESEND_COOLDOWN_SECONDS)
                ->getTimestamp() - now()->getTimestamp()
        );
    }

    /**
     * Remove any outstanding code for this address and purpose.
     */
    public function clear(string $email, string $purpose): void
    {
        EmailVerificationCode::query()
            ->where('email', $this->normalizeEmail($email))
            ->where('purpose', $purpose)
            ->delete();
    }

    /**
     * User-facing message for a failed verify() result.
     */
    public static function errorMessage(string $status): string
    {
        return match ($status) {
            self::VERIFY_INVALID => 'That code is incorrect. Please check the 6-digit code and try again.',
            self::VERIFY_EXPIRED => 'That code has expired. Please request a new code.',
            self::VERIFY_LOCKED => 'Too many incorrect attempts. Please request a new code.',
            default => 'We could not find a verification code for this email. Please request a new code.',
        };
    }

    protected function generateCode(): string
    {
        return str_pad(
            (string) random_int(0, (10 ** self::CODE_LENGTH) - 1),
            self::CODE_LENGTH,
            '0',
            STR_PAD_LEFT
        );
    }

    protected function normalizeEmail(string $email): string
    {
        return Str::lower(trim($email));
    }

    protected function assertPurpose(string $purpose): void
    {
        if (! in_array($purpose, [self::PURPOSE_REGISTER, self::PURPOSE_RESET], true)) {
            throw new InvalidArgumentException("Unsupported OTP purpose: {$purpose}");
        }
    }

    protected function templateSlug(string $purpose): string
    {
        return match ($purpose) {
            self::PURPOSE_REGISTER => 'registration-verification-code',
            self::PURPOSE_RESET => 'password-reset-verification-code',
        };
    }

    protected function sendRateKey(string $email, string $purpose): string
    {
        return 'otp-send:'.sha1($email.'|'.$purpose.'|'.request()->ip());
    }
}
