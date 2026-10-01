<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventRsvpSeeder extends Seeder
{
    /**
     * Per-event participation range as a percentage of all alumni.
     * Actual number is picked randomly within [min, max] and capped
     * by the event's `capacity` when set.
     */
    protected float $minRespondRate = 0.15;   // 15%

    protected float $maxRespondRate = 0.65;   // 65%

    /**
     * Of those who said YES, how many actually showed up?
     * Only applies to events that have already happened.
     */
    protected float $attendanceRate = 0.75;   // 75% of yes RSVPs attended

    public function run(): void
    {
        // RSVPs are only for real events (skip drafts).
        $events = Event::query()
            ->where('status', '!=', 'draft')
            ->get();

        if ($events->isEmpty()) {
            $this->command->error('→ RSVPs: SKIPPED — no published/completed events found. Run EventSeeder first.');

            return;
        }

        // Only active alumni RSVP. Deactivated accounts can't log in.
        $alumni = User::query()
            ->whereHas('roles', fn ($q) => $q->where('name', 'alumni'))
            ->where('is_active', true)
            ->pluck('id');

        if ($alumni->isEmpty()) {
            $this->command->error('→ RSVPs: SKIPPED — no active alumni found.');

            return;
        }

        // Self-clearing.
        if (EventRsvp::query()->exists()) {
            $this->command->warn('→ RSVPs: clearing existing rows before re-seeding.');
            EventRsvp::query()->delete();
        }

        $alumniCount = $alumni->count();

        $bar = $this->command->getOutput()->createProgressBar($events->count());
        $bar->start();

        foreach ($events as $event) {
            $this->seedRsvpsForEvent($event, $alumni, $alumniCount);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('  ✓ RSVPs: '.EventRsvp::count());
        $this->command->info('    → Attended: '.EventRsvp::whereNotNull('attended_at')->count());
    }

    // =========================================================
    //  PER-EVENT
    // =========================================================

    protected function seedRsvpsForEvent(Event $event, $alumniIds, int $alumniCount): void
    {
        // How many alumni respond to this event?
        $respondRate = fake()->randomFloat(2, $this->minRespondRate, $this->maxRespondRate);
        $respondCount = (int) round($alumniCount * $respondRate);

        // Respect the event's capacity for "yes" responses when set.
        $capacity = $event->capacity ?? $respondCount;

        // Never try to draw more responders than alumni exist.
        $respondCount = min($respondCount, $alumniCount);

        $responders = $alumniIds->random($respondCount);

        // Is this event in the past? Only past events can have attendance.
        $isPast = $event->starts_at && $event->starts_at->isPast();

        foreach ($responders as $userId) {
            [$response, $respondedAt] = $this->pickResponse($event);

            // attended_at is only set when:
            //  • the event is in the past
            //  • the alumni said yes
            //  • and they actually showed up (75% of yeses)
            $attendedAt = null;
            if ($isPast && $response === 'yes' && fake()->boolean((int) ($this->attendanceRate * 100))) {
                $attendedAt = (clone $event->starts_at)->modify('+'.fake()->numberBetween(0, 15).' minutes');
            }

            EventRsvp::create([
                'event_id' => $event->id,
                'user_id' => $userId,
                'response' => $response,
                'responded_at' => $respondedAt,
                'attended_at' => $attendedAt,
                'notes' => fake()->boolean(10) ? $this->randomNote() : null,
            ]);
        }
    }

    // =========================================================
    //  RESPONSE DISTRIBUTION
    // =========================================================

    /**
     * Weighted response picker:
     *   yes   → 65%
     *   maybe → 20%
     *   no    → 15%
     *
     * Returns [response, responded_at].
     */
    protected function pickResponse(Event $event): array
    {
        $roll = fake()->numberBetween(1, 100);

        $response = match (true) {
            $roll <= 65 => 'yes',
            $roll <= 85 => 'maybe',
            default => 'no',
        };

        // Responded_at anchors on the event's start, clamped so it never
        // lands in the future. Works for both upcoming and past events.
        //
        //   • Upcoming event → responded within the last 45 days.
        //   • Past event     → responded in the 45 days before it started.
        $startsAt = $event->starts_at ?? now();
        $latest = min(now(), $startsAt);
        $earliest = (clone $latest)->modify('-45 days');

        $respondedAt = fake()->dateTimeBetween($earliest, $latest);

        return [$response, $respondedAt];
    }

    // =========================================================
    //  NOTES
    // =========================================================

    protected function randomNote(): string
    {
        return fake()->randomElement([
            'Looking forward to this!',
            'Will bring two batchmates.',
            'Might be late — coming from work.',
            'Can I bring a plus-one?',
            'Please reserve a parking slot if possible.',
            'Happy to help with the setup.',
            'Will confirm attendance closer to the date.',
            'Sorry, out of town that week.',
            'Can we get a copy of the program?',
            'Excited to reconnect with everyone!',
        ]);
    }
}
