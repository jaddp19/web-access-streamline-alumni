<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class EmailTemplateService
{
    public static function send(string $slug, string $toEmail, array $placeholders = []): void
    {
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

    protected static function merge(string $text, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }

        return $text;
    }
}
