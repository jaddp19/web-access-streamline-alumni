<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
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

        $record = EmailTemplate::query()
            ->whereJsonContains('template->slug', $slug)
            ->first();

        if (! $record) {
            logger()->warning("Email template not found for slug: {$slug}");
            return;
        }

        $subject = self::merge($record->template['subject'] ?? '', $placeholders);
        $body    = self::merge($record->template['message'] ?? '', $placeholders);

        Mail::to($toEmail)->queue(new TemplatedMail($subject, $body));
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

        $profile = \App\Models\UserProfile::query()
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
            'welcome-registrar',
            'your-csav-alumni-network-staff-account-has-been-created',
            'password-reset',
            'password-reset-by-registrar',
        ];
    }

    protected static function merge(string $text, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }

        return $text;
    }
}