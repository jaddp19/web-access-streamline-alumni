<?php

namespace App\Jobs;

use App\Models\EmailTemplate;
use App\Models\Event;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEventCancellationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;         // no retries — partial blasts shouldn't re-send
    public int $timeout = 900;     // 15 minutes

    public function __construct(public int $eventId) {}

    public function handle(): void
    {
        $event = Event::find($this->eventId);

        if (! $event) {
            Log::warning('Event cancellation: event not found', ['event_id' => $this->eventId]);
            return;
        }

        if ($event->status !== 'cancelled') {
            Log::info('Event cancellation: skipped (not cancelled)', [
                'event_id' => $event->id,
                'status'   => $event->status,
            ]);
            return;
        }

        // Fail fast if the template doesn't exist
        $templateExists = EmailTemplate::query()
            ->where('template->slug', 'event-has-been-cancelled')
            ->exists();

        if (! $templateExists) {
            Log::error('Event cancellation: template missing — no emails sent', [
                'event_id' => $event->id,
                'slug'     => 'event-has-been-cancelled',
            ]);
            return;
        }

        Log::info('Event cancellation: blast starting', [
            'event_id' => $event->id,
        ]);

        // Only attendees who said yes or maybe
        $attendees = $event->rsvps()
            ->whereIn('response', ['yes', 'maybe'])
            ->with('user:id,name,email')
            ->get();

        $sent   = 0;
        $failed = 0;

        foreach ($attendees as $rsvp) {
            $user = $rsvp->user;

            if (! $user || ! $user->email) {
                continue;
            }

            try {
                EmailTemplateService::send(
                    'event-has-been-cancelled',
                    $user->email,
                    [
                        'name'        => $user->name,
                        'event_title' => $event->title,
                        'event_date'  => $event->starts_at
                            ? $event->starts_at->format('F j, Y \a\t g:i A')
                            : 'TBA',
                    ]
                );

                $sent++;

                usleep(200_000); // 200ms — max 5 emails/sec
            } catch (\Throwable $e) {
                $failed++;

                Log::warning('Event cancellation: individual send failed', [
                    'event_id' => $event->id,
                    'email'    => $user->email,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        Log::info('Event cancellation: blast complete', [
            'event_id'     => $event->id,
            'total_sent'   => $sent,
            'total_failed' => $failed,
        ]);

        if ($failed > 0) {
            Log::error('Event cancellation: SOME EMAILS FAILED', [
                'event_id'     => $event->id,
                'failed_count' => $failed,
                'sent_count'   => $sent,
            ]);
        }
    }
}