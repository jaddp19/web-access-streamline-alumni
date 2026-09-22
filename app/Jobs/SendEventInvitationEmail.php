<?php

namespace App\Jobs;

use App\Models\Department;
use App\Models\EmailTemplate;
use App\Models\Event;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendEventInvitationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;         // no retries — partial blasts shouldn't re-send
    public int $timeout = 900;     // 15 minutes for large batches

    public function __construct(public int $eventId) {}

    public function handle(): void
    {
        $event = Event::with('creator:id,name')->find($this->eventId);

        if (! $event) {
            Log::warning('Event invitation: event not found', ['event_id' => $this->eventId]);
            return;
        }

        if ($event->status !== 'published') {
            Log::info('Event invitation: skipped (not published)', [
                'event_id' => $event->id,
                'status'   => $event->status,
            ]);
            return;
        }

        // Fail fast if the template doesn't exist
        $templateExists = EmailTemplate::query()
            ->whereJsonContains('template->slug', 'youre-invited-event-invitation')
            ->exists();

        if (! $templateExists) {
            Log::error('Event invitation: template missing — no emails sent', [
                'event_id' => $event->id,
                'slug'     => 'youre-invited-event-invitation',
            ]);
            return;
        }

        // =========================================================
        //  RECIPIENT SCOPING
        //  · Registrar / Super Admin  → all alumni
        //  · Program Head             → only their department's alumni
        // =========================================================

        $creator       = $event->creator;
        $isProgramHead = $creator?->hasRole('program head') ?? false;
        $departmentId  = null;

        if ($isProgramHead) {
            $departmentId = Department::query()
                ->where('program_head_id', $creator->id)
                ->value('id');

            // Program head without a department → nothing to send
            if (! $departmentId) {
                Log::warning('Event invitation: program head has no department — no emails sent', [
                    'event_id'   => $event->id,
                    'creator_id' => $creator->id,
                ]);
                return;
            }
        }

        Log::info('Event invitation: blast starting', [
            'event_id'        => $event->id,
            'created_by'      => $creator?->id,
            'is_program_head' => $isProgramHead,
            'department_id'   => $departmentId,
        ]);

        // =========================================================
        //  BUILD QUERY
        // =========================================================

        $recipients = User::role('alumni')
            ->whereNotNull('email')
            ->select('id', 'name', 'email');

        if ($isProgramHead && $departmentId) {
            $recipients->whereHas('userProfile.courses', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        // =========================================================
        //  SEND
        // =========================================================

        $sent       = 0;
        $failed     = 0;
        $failedList = [];

        $recipients->chunkById(200, function ($alumni) use ($event, &$sent, &$failed, &$failedList) {
            foreach ($alumni as $u) {
                try {
                    EmailTemplateService::send(
                        'youre-invited-event-invitation',
                        $u->email,
                        [
                            'name'           => $u->name,
                            'event_title'    => $event->title,
                            'event_date'     => $event->starts_at->format('F j, Y \a\t g:i A'),
                            'event_location' => $event->location ?? 'TBA',
                            'event_excerpt'  => Str::limit(strip_tags($event->description ?? ''), 180),
                            'rsvp_url'       => route('login'),
                        ]
                    );

                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    $failedList[] = $u->email;

                    Log::warning('Event invitation: individual send failed', [
                        'event_id' => $event->id,
                        'email'    => $u->email,
                        'error'    => $e->getMessage(),
                    ]);
                }
            }

            // Progress log every chunk of 200
            Log::info('Event invitation: progress', [
                'event_id' => $event->id,
                'sent'     => $sent,
                'failed'   => $failed,
            ]);
        });

        // Final summary
        Log::info('Event invitation: blast complete', [
            'event_id'      => $event->id,
            'total_sent'    => $sent,
            'total_failed'  => $failed,
            'failed_sample' => array_slice($failedList, 0, 10),
        ]);

        if ($failed > 0) {
            Log::error('Event invitation: SOME EMAILS FAILED', [
                'event_id'     => $event->id,
                'failed_count' => $failed,
                'sent_count'   => $sent,
            ]);
        }
    }
}