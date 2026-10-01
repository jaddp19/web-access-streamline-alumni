<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    /** How many events to create. */
    protected int $count = 20;

    public function run(): void
    {
        // Only registrar + program head can author events.
        $authors = User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['registrar', 'program head']))
            ->where('is_active', true)
            ->get();

        if ($authors->isEmpty()) {
            $this->command->error('→ Events: SKIPPED — no registrar or program head found.');
            return;
        }

        if (Event::query()->exists()) {
            $this->command->warn('→ Events: already seeded — skipping.');
            return;
        }

        $bar = $this->command->getOutput()->createProgressBar($this->count);
        $bar->start();

        $titles = $this->sampleTitles();

        for ($i = 0; $i < $this->count; $i++) {
            $author = $authors->random();

            // Mix: 50% upcoming · 30% past/completed · 10% cancelled · 10% draft
            $roll = fake()->numberBetween(1, 100);
            $state = match (true) {
                $roll <= 50 => 'upcoming',
                $roll <= 80 => 'past',
                $roll <= 90 => 'cancelled',
                default     => 'draft',
            };

            $title = $titles[$i % count($titles)];

            [$startsAt, $endsAt, $deadline, $status] = $this->timingFor($state);

            Event::create([
                'title'                 => $title,
                'slug'                  => $this->uniqueSlug($title),
                'description'           => $this->description($state),
                'image'                 => null, // seeded events have no banner image
                'starts_at'             => $startsAt,
                'ends_at'               => $endsAt,
                'registration_deadline' => $deadline,
                'location'              => $this->location(),
                'capacity'              => fake()->boolean(60) ? fake()->randomElement([30, 50, 75, 100, 150, 200]) : null,
                'status'                => $status,
                'created_by'            => $author->id,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('  ✓ Events: '.Event::count());
    }

    // =========================================================
    //  TIMING
    // =========================================================

    /**
     * Returns [starts_at, ends_at, registration_deadline, status]
     * for the given state.
     */
    protected function timingFor(string $state): array
    {
        return match ($state) {
            'upcoming' => $this->upcomingTiming(),
            'past'     => $this->pastTiming(),
            'cancelled' => $this->cancelledTiming(),
            'draft'    => $this->draftTiming(),
            default    => $this->upcomingTiming(),
        };
    }

    protected function upcomingTiming(): array
    {
        $starts = fake()->dateTimeBetween('+3 days', '+6 months');
        $ends   = (clone $starts)->modify('+'.fake()->numberBetween(2, 8).' hours');
        $deadline = (clone $starts)->modify('-'.fake()->numberBetween(1, 3).' days');

        return [$starts, $ends, $deadline, 'published'];
    }

    protected function pastTiming(): array
    {
        $starts = fake()->dateTimeBetween('-1 year', '-7 days');
        $ends   = (clone $starts)->modify('+'.fake()->numberBetween(2, 8).' hours');
        $deadline = (clone $starts)->modify('-2 days');

        return [$starts, $ends, $deadline, 'completed'];
    }

    protected function cancelledTiming(): array
    {
        $starts = fake()->dateTimeBetween('-3 months', '+2 months');
        $ends   = (clone $starts)->modify('+4 hours');
        $deadline = (clone $starts)->modify('-2 days');

        return [$starts, $ends, $deadline, 'cancelled'];
    }

    protected function draftTiming(): array
    {
        // Draft events don't show anywhere public — arbitrary future date.
        $starts = fake()->dateTimeBetween('+1 month', '+1 year');
        $ends   = (clone $starts)->modify('+4 hours');
        $deadline = (clone $starts)->modify('-3 days');

        return [$starts, $ends, $deadline, 'draft'];
    }

    // =========================================================
    //  CONTENT
    // =========================================================

    protected function sampleTitles(): array
    {
        return [
            'Alumni Homecoming 2026',
            'CSAV Grand Alumni Reunion',
            'Career Talk: Life After Graduation',
            'Job Fair & Industry Networking',
            'Alumni Sports Fest',
            'Homecoming Golf Tournament',
            'Tracer Study Orientation',
            'Board Exam Review Kickoff',
            'Alumni Mentorship Program Launch',
            'CSAV Foundation Fundraising Dinner',
            'Alumni Chapter Meet & Greet — Manila',
            'Alumni Chapter Meet & Greet — Cebu',
            'Continuing Professional Development Seminar',
            'Alumni Awards Night',
            'Christmas Fellowship & Gift-Giving',
            'Founder\'s Day Celebration',
            'Alumni Basketball League Opening',
            'Graduate School Information Session',
            'Batch Representatives Assembly',
            'Alumni-Led Skills Workshop',
        ];
    }

    protected function location(): string
    {
        return fake()->randomElement([
            'CSAV Main Campus — Gymnasium',
            'CSAV Main Campus — Auditorium',
            'CSAV Multi-Purpose Hall',
            'Victorias City Convention Center',
            'CSAV Alumni Lounge',
            'CSAV Covered Court',
            'Victorias City Sports Complex',
            'Online — Zoom Webinar',
            'CSAV Library Function Room',
        ]);
    }

    protected function description(string $state): string
    {
        $openers = [
            'The Colegio de Sta. Ana de Victorias Alumni Office invites all graduates to join us for',
            'Mark your calendars — the CSAV Alumni Network is hosting',
            'In partnership with the CSAV Foundation, we are pleased to present',
            'Calling all alumni! Join us for',
        ];

        $body = match ($state) {
            'cancelled' => 'Please note that this event has been cancelled. We apologize for any inconvenience. A rescheduled date will be announced soon.',
            'draft'     => 'Details are still being finalized. This event is not yet open for registration.',
            default     => 'Come reconnect with classmates, meet fellow alumni, and hear updates from the college. Refreshments will be served. Registration is required to reserve your slot.',
        };

        return fake()->randomElement($openers).' a meaningful gathering of the CSAV alumni community.'."\n\n".$body;
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (Event::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}