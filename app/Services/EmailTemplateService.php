<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Mail;

class EmailTemplateService
{
    public static function send(string $slug, string $toEmail, array $placeholders = []): void
    {
        // ── Notification preference gate ─────────────────────────────
        if (! self::shouldSend($toEmail, $slug)) {
            return;
        }
        // ─────────────────────────────────────────────────────────────

        $record = self::template($slug);

        if (! $record) {
            logger()->warning("Email template not found for slug: {$slug}");

            return;
        }

        [$subject, $body] = self::render($record, $placeholders);

        Mail::to($toEmail)->queue(new TemplatedMail($subject, $body));
    }

    /**
     * Send a templated email immediately instead of queueing it.
     *
     * Used for time-sensitive security codes. Notification preferences are
     * intentionally bypassed: the recipient cannot opt out of a code they
     * just asked for. Throws when the template is missing so the caller can
     * tell the user the code was not sent.
     */
    public static function sendNow(string $slug, string $toEmail, array $placeholders = []): void
    {
        $record = self::template($slug);

        if (! $record) {
            throw new \RuntimeException("Email template not found for slug: {$slug}");
        }

        [$subject, $body] = self::render($record, $placeholders);

        Mail::to($toEmail)->send(new TemplatedMail($subject, $body));
    }

    /**
     * True when the recipient should receive this email.
     * Critical/transactional templates always bypass the preference.
     */
    protected static function shouldSend(string $email, string $slug): bool
    {
        if (in_array($slug, self::criticalSlugs(), true)) {
            return true;
        }

        $profile = UserProfile::query()
            ->whereHas('user', fn ($q) => $q->where('email', $email))
            ->first();

        // No profile → fail-open (better to send than silently drop).
        return $profile?->email_notifications ?? true;
    }

    /**
     * Templates that always bypass the user's preference.
     * Account / security — user can't be locked out by unchecking a box.
     */
    protected static function criticalSlugs(): array
    {
        return [
            'welcome-to-the-csav-alumni-network-name',
            'welcome-program-head',
            'welcome-alumni-coordinator',
            'your-csav-alumni-network-staff-account-has-been-created',
            'password-reset',
            'password-reset-by-alumni-coordinator',
            'registration-verification-code',
            'password-reset-verification-code',
        ];
    }

    protected static function template(string $slug): ?EmailTemplate
    {
        return EmailTemplate::query()
            ->whereJsonContains('template->slug', $slug)
            ->first();
    }

    /**
     * @return array{0: string, 1: string} [subject, body]
     */
    protected static function render(EmailTemplate $record, array $placeholders): array
    {
        return [
            self::merge($record->template['subject'] ?? '', $placeholders),
            self::merge($record->template['message'] ?? '', $placeholders),
        ];
    }

    protected static function merge(string $text, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $text = str_replace('{{'.$key.'}}', $value, $text);
        }

        return $text;
    }
}
