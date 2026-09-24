<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Category;
use App\Models\CivilStatusEmployment;
use App\Models\Company;
use App\Models\Course;
use App\Models\Department;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\FurtherStudy;
use App\Models\Post;
use App\Models\TracerStudy;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    /** Number of alumni to create per course. */
    protected int $alumniPerCourse = 50;

    /** Percentage of alumni who end up "employed" (get work history). */
    protected int $employedPercent = 70;

    /** Number of events to create. */
    protected int $eventCount = 10;

    /** Number of posts to create. */
    protected int $postCount = 15;

    public function run(): void
    {
        $this->command->info('→ Seeding demo data...');

        if (User::role('program head')->count() > 0 && User::role('alumni')->count() > 100) {
            $this->command->warn('Demo data already appears to exist. Skipping.');
            return;
        }

        $this->seedProgramHeads();
        $this->seedAlumni();
        $this->seedEventsAndRsvps();
        $this->seedPosts();

        $this->command->info('✓ Demo seeding complete.');
        $this->command->info('  Alumni:       ' . User::role('alumni')->count());
        $this->command->info('  Tracer:       ' . TracerStudy::count());
        $this->command->info('  Program Head: ' . User::role('program head')->count());
        $this->command->info('  Events:       ' . Event::count());
        $this->command->info('  Posts:        ' . Post::count());
    }

    // =========================================================
    //  1. ONE PROGRAM HEAD PER DEPARTMENT
    // =========================================================

    protected function seedProgramHeads(): void
    {
        $departments = Department::all();

        $this->command->info("→ Program heads: 1 per department ({$departments->count()})");

        foreach ($departments as $dept) {
            if ($dept->program_head_id) {
                continue;
            }

            $user = User::factory()->programHead()->create();
            UserProfile::factory()->create(['user_id' => $user->id]);

            $dept->update(['program_head_id' => $user->id]);
        }
    }

    // =========================================================
    //  2. 50 ALUMNI PER COURSE — ALL with tracer studies
    // =========================================================

    protected function seedAlumni(): void
    {
        $courses = Course::query()->where('is_active', true)->get();
        $batches = Batch::query()->pluck('id');
        $companies = Company::query()->pluck('id');

        if ($companies->isEmpty()) {
            Company::factory()->count(30)->create();
            $companies = Company::query()->pluck('id');
        }

        $this->command->info("→ Alumni: {$this->alumniPerCourse} per course, {$courses->count()} courses");

        $bar = $this->command->getOutput()->createProgressBar($courses->count() * $this->alumniPerCourse);
        $bar->start();

        foreach ($courses as $course) {
            for ($i = 0; $i < $this->alumniPerCourse; $i++) {
                DB::transaction(function () use ($course, $batches, $companies) {
                    $user = User::factory()->alumni()->create();

                    // Profile factory — add board exam fields for board courses
                    $profileFactory = UserProfile::factory();

                    if ($course->course_type === 'board') {
                        // Not every board-course alumni has taken the exam yet (~20% pending)
                        if (fake()->boolean(80)) {
                            $profileFactory = $profileFactory->withBoardExam();
                        }
                    }

                    $profile = $profileFactory->create([
                        'user_id'  => $user->id,
                        'batch_id' => $batches->random(),
                    ]);

                    $profile->courses()->attach($course->id);

                    // ── EVERY alumni gets a tracer study ─────────────
                    $this->attachEmploymentData($user, $companies);
                    // ─────────────────────────────────────────────────
                });

                $bar->advance();
            }
        }

        $bar->finish();
        $this->command->newLine();
    }

    /**
     * Creates a tracer study + employment record + further study for an alumni.
     * Every alumni gets one — the "employedPercent" only affects whether they
     * also get a WorkHistory row.
     */
    protected function attachEmploymentData(User $user, $companies): void
    {
        $employmentStatus = fake()->randomElement([
            'employed', 'employed', 'employed', 'employed', 'employed',
            'self-employed', 'unemployed', 'other',
        ]);

        $isEmployed = in_array($employmentStatus, ['employed', 'self-employed'], true);

        $tracer = TracerStudy::create(['user_id' => $user->id]);

        CivilStatusEmployment::create([
            'tracer_study_id'            => $tracer->id,
            'civil_status'               => fake()->randomElement(['single', 'married', 'widowed', 'separated', 'single-parent']),
            'employment_status'          => $employmentStatus,
            'current_job_position'       => $isEmployed ? fake()->jobTitle() : null,
            'employed_related_to_degree' => $isEmployed ? fake()->randomElement(['yes', 'no', 'partially-related']) : null,
            'employment_type'            => $isEmployed ? fake()->randomElement(['full-time', 'part-time', 'contractual-project-based', 'freelance', 'other']) : null,
            'organization_type'          => $isEmployed ? fake()->randomElement(['private-company', 'government-agency', 'non-government-organization', 'educational-institution', 'self-employed-business', 'other']) : null,
            'employment_area'            => $isEmployed ? fake()->randomElement(['philippines', 'philippines', 'philippines', 'abroad']) : null,
            'abroad_country'             => $isEmployed && fake()->boolean(20) ? fake()->country() : null,
            'months_to_first_job'        => $isEmployed ? fake()->randomElement(['1-3-months', '4-6-months', 'more-than-6-months', 'more-than-1-year']) : null,
        ]);

        $pursued = fake()->boolean(25);
        FurtherStudy::create([
            'tracer_study_id'             => $tracer->id,
            'is_pursued_further_studies'  => $pursued,
            'level_of_study'              => $pursued ? fake()->randomElement(['Bachelor', 'Master', 'Certificate', 'Post Doctorate']) : null,
        ]);

        // Work history only for those who are employed AND get a "full profile"
        // (this is where the 70% comes in — not everyone has a current job)
        if ($isEmployed && fake()->boolean($this->employedPercent) && $companies->isNotEmpty()) {
            WorkHistory::create([
                'user_id'        => $user->id,
                'work_name'      => fake()->jobTitle(),
                'company_id'     => $companies->random(),
                'date_hired'     => fake()->dateTimeBetween('-5 years', 'now'),
                'is_current_job' => true,
            ]);
        }
    }

    // =========================================================
    //  3. EVENTS + RSVPs
    // =========================================================

    protected function seedEventsAndRsvps(): void
    {
        $registrar = User::role('registrar')->first();
        $alumni    = User::role('alumni')->get();

        if (! $registrar || $alumni->isEmpty()) {
            return;
        }

        $this->command->info("→ Events: {$this->eventCount}");

        for ($i = 0; $i < $this->eventCount; $i++) {
            $event = Event::factory()->create([
                'created_by' => $registrar->id,
            ]);

            $responders = $alumni->random(min($alumni->count(), fake()->numberBetween(
                (int) ($alumni->count() * 0.3),
                (int) ($alumni->count() * 0.8),
            )));

            foreach ($responders as $responder) {
                EventRsvp::factory()->create([
                    'event_id' => $event->id,
                    'user_id'  => $responder->id,
                ]);
            }
        }
    }

    // =========================================================
    //  4. POSTS
    // =========================================================

    protected function seedPosts(): void
    {
        $authors = User::role('registrar')
            ->orWhere('email', 'like', '%program-head%')
            ->get();

        if ($authors->isEmpty()) {
            $authors = User::role('program head')->get();
        }

        if ($authors->isEmpty()) {
            return;
        }

        $this->command->info("→ Posts: {$this->postCount}");

        $categories = Category::pluck('id');

        for ($i = 0; $i < $this->postCount; $i++) {
            Post::factory()->create([
                'user_id'     => $authors->random()->id,
                'category_id' => $categories->random(),
            ]);
        }
    }
}