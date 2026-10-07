<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\BoardExam;
use App\Models\CivilStatusEmployment;
use App\Models\Company;
use App\Models\Course;
use App\Models\Department;
use App\Models\FurtherStudy;
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

    /**
     * Approval distribution for seeded alumni.
     * Must sum to 100.
     */
    protected int $approvedPercent = 85;

    protected int $pendingPercent = 10;
    // remainder is rejected

    public function run(): void
    {
        $this->command->info('→ Seeding demo data...');

        // Registrar must exist before other seeded content can reference it.
        $this->seedRegistrarIfMissing();

        // Only skip the alumni-heavy part on re-runs.
        $hasAlumni = User::role('alumni')->count() > 100;

        if (! $hasAlumni) {
            $this->seedProgramHeads();
            $this->seedAlumni();
        } else {
            $this->command->warn('Alumni already seeded — skipping program heads + alumni.');
        }

        $this->command->info('✓ Demo seeding complete.');
        $this->command->info('  Alumni:            '.User::role('alumni')->count());
        $this->command->info('    → Approved:      '.UserProfile::where('is_approved', true)->whereHas('user', fn ($q) => $q->role('alumni'))->count());
        $this->command->info('    → Pending:       '.UserProfile::where('is_approved', false)->whereNull('last_rejection_reason')->whereHas('user', fn ($q) => $q->role('alumni'))->count());
        $this->command->info('    → Rejected:      '.UserProfile::where('is_approved', false)->whereNotNull('last_rejection_reason')->whereHas('user', fn ($q) => $q->role('alumni'))->count());
        $this->command->info('  Board attempts:    '.BoardExam::count());
        $this->command->info('    → Verified:      '.BoardExam::where('is_verified', true)->count());
        $this->command->info('    → Pending:       '.BoardExam::where('is_verified', false)->count());
        $this->command->info('    → Top notchers:  '.BoardExam::where('is_top_notcher', true)->count());
        $this->command->info('  Tracer:            '.TracerStudy::count());
        $this->command->info('  Program Head:      '.User::role('program head')->count());
    }

    // =========================================================
    //  0. REGISTRAR (created on demand if missing)
    // =========================================================

    protected function seedRegistrarIfMissing(): void
    {
        if (User::role('registrar')->exists()) {
            return;
        }

        $registrar = User::factory()->registrar()->create();

        // Staff profiles are approved by default — they're not in the review pipeline.
        UserProfile::factory()->approved()->create(['user_id' => $registrar->id]);

        $this->command->info('→ Registrar: created (was missing)');
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

            // Staff profiles are approved — they don't go through the alumni review.
            UserProfile::factory()->approved()->create(['user_id' => $user->id]);

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
        $this->command->info("  Approval mix: {$this->approvedPercent}% approved · {$this->pendingPercent}% pending · ".(100 - $this->approvedPercent - $this->pendingPercent).'% rejected');

        $bar = $this->command->getOutput()->createProgressBar($courses->count() * $this->alumniPerCourse);
        $bar->start();

        foreach ($courses as $course) {
            for ($i = 0; $i < $this->alumniPerCourse; $i++) {
                DB::transaction(function () use ($course, $batches, $companies) {
                    $user = User::factory()->alumni()->create();

                    // ── Approval state (weighted random) ────────────────
                    $roll = fake()->numberBetween(1, 100);

                    $approvalState = match (true) {
                        $roll <= $this->approvedPercent => 'approved',

                        $roll <= $this->approvedPercent + $this->pendingPercent => 'pending',

                        default => 'rejected',
                    };

                    $profileFactory = match ($approvalState) {
                        'approved' => UserProfile::factory()->approved(),
                        'pending' => UserProfile::factory()->pending(),
                        'rejected' => UserProfile::factory()->rejected(),
                    };
                    // ────────────────────────────────────────────────────

                    $profile = $profileFactory->create([
                        'user_id' => $user->id,
                        'batch_id' => $batches->random(),
                    ]);

                    $profile->courses()->attach($course->id);

                    // ── Board exam attempts (only for board courses) ────
                    if ($course->course_type === 'board') {
                        $this->seedBoardAttemptsFor($profile, $course, $approvalState);
                    }
                    // ────────────────────────────────────────────────────

                    // ── EVERY alumni gets a tracer study ────────────────
                    $this->attachEmploymentData($user, $companies);
                    // ────────────────────────────────────────────────────
                });

                $bar->advance();
            }
        }

        $bar->finish();
        $this->command->newLine();
    }

    /**
     * Creates 1–2 realistic board_exams rows for a board-course alumni and
     * syncs the profile's mirror columns + is_verified flag.
     *
     * Verification state is derived from the alumni's approval state:
     *   approved → every attempt is verified (has at least one passing attempt)
     *   pending  → only the LATEST attempt is unverified (shows in queue);
     *              every earlier attempt is verified history
     *   rejected → same as pending
     *
     * Rule: only ONE unverified row per alumni at any time. If an alumni
     * retook the exam, the earlier attempts were already reviewed by the
     * registrar — they MUST be is_verified = true so the queue shows one
     * row, not the entire attempt chain.
     */
    protected function seedBoardAttemptsFor(UserProfile $profile, Course $course, string $approvalState): void
    {
        // Top notchers pass on their first (and only) take.
        $isTopNotcher = $approvalState === 'approved' && fake()->boolean(10);
        $attemptCount = $isTopNotcher ? 1 : fake()->numberBetween(1, 2);

        for ($n = 1; $n <= $attemptCount; $n++) {
            $isFinalAttempt = ($n === $attemptCount);

            $baseState = [
                'user_profile_id' => $profile->id,
                'attempt_number'  => $n,
                'exam_name'       => $course->course_title,
            ];

            if ($approvalState === 'approved') {
                // Approved — every attempt is verified. Retakers only passed
                // on the final take.
                $factory = match (true) {
                    $isTopNotcher && $isFinalAttempt       => BoardExam::factory()->topNotcher(),
                    $isFinalAttempt && fake()->boolean(85) => BoardExam::factory()->verifiedPassing(),
                    default                                 => BoardExam::factory()->verifiedFailing(),
                };

                $factory->state($baseState)->create();

                continue;
            }

            // ── Pending / rejected ─────────────────────────────────────
            if ($isFinalAttempt) {
                // The ONE pending row awaiting registrar review.
                $passing = fake()->boolean(85);

                BoardExam::factory()->state(array_merge($baseState, [
                    'rate'        => $passing
                        ? fake()->randomFloat(2, 75, 99)
                        : fake()->randomFloat(2, 60, 74.99),
                    'passed'      => $passing,
                    'is_verified' => false,
                    'verified_at' => null,
                ]))->create();
            } else {
                // Historical attempt → already reviewed (and failed —
                // otherwise the alumni would be approved, not pending).
                BoardExam::factory()->state(array_merge($baseState, [
                    'rate'        => fake()->randomFloat(2, 60, 74.99),
                    'passed'      => false,
                    'is_verified' => true,
                    'verified_at' => now()->subMonths(fake()->numberBetween(6, 36)),
                ]))->create();
            }
        }

        $profile->refresh();
        $profile->syncBoardMirrors();

        // Approved board alumni are always "verified" — the registrar
        // already reviewed them, pass or fail. Without this, failed-only
        // alumni end up with is_verified = false after syncBoardMirrors()
        // (which keys off "has a verified PASSING attempt") and they vanish
        // from the dashboard's Passed/Failed breakdown.
        if ($approvalState === 'approved') {
            $profile->forceFill(['is_verified' => true])->saveQuietly();
        }
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

        // ── "Other" — pick a realistic free-text reason ────────────
        $employmentStatusOther = $employmentStatus === 'other'
            ? fake()->randomElement([
                'Retired',
                'Studying full-time',
                'Caregiver',
                'Homemaker',
                'On a career break',
                'Volunteering abroad',
            ])
            : null;
        // ────────────────────────────────────────────────────────────

        $tracer = TracerStudy::create(['user_id' => $user->id]);

        CivilStatusEmployment::create([
            'tracer_study_id' => $tracer->id,
            'civil_status' => fake()->randomElement(['single', 'married', 'widowed', 'separated', 'single-parent']),
            'employment_status' => $employmentStatus,
            'employment_status_other' => $employmentStatusOther,
            'current_job_position' => $isEmployed ? fake()->jobTitle() : null,
            'employed_related_to_degree' => $isEmployed ? fake()->randomElement(['yes', 'no', 'partially-related']) : null,
            'employment_type' => $isEmployed ? fake()->randomElement(['full-time', 'part-time', 'contractual-project-based', 'freelance', 'other']) : null,
            'organization_type' => $isEmployed ? fake()->randomElement(['private-company', 'government-agency', 'non-government-organization', 'educational-institution', 'self-employed-business', 'other']) : null,
            'employment_area' => $isEmployed ? fake()->randomElement(['philippines', 'philippines', 'philippines', 'abroad']) : null,
            'abroad_country' => $isEmployed && fake()->boolean(20) ? fake()->country() : null,
            'months_to_first_job' => $isEmployed ? fake()->randomElement(['1-3-months', '4-6-months', 'more-than-6-months', 'more-than-1-year']) : null,
        ]);

        $pursued = fake()->boolean(25);

        FurtherStudy::create([
            'tracer_study_id' => $tracer->id,
            'is_pursued_further_studies' => $pursued,
            'level_of_study' => $pursued ? fake()->randomElement(['Bachelor', 'Master', 'Certificate', 'Post Doctorate']) : null,
        ]);

        // Work history only for those who are employed AND get a "full profile"
        if ($isEmployed && fake()->boolean($this->employedPercent) && $companies->isNotEmpty()) {
            WorkHistory::create([
                'user_id' => $user->id,
                'work_name' => fake()->jobTitle(),
                'company_id' => $companies->random(),
                'date_hired' => fake()->dateTimeBetween('-5 years', 'now'),
                'is_current_job' => true,
            ]);
        }
    }
}