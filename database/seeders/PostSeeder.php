<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /** How many posts to create. */
    protected int $count = 25;

    public function run(): void
    {
        // Only registrar + program head can author posts.
        $authors = User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['registrar', 'program head']))
            ->where('is_active', true)
            ->get();

        if ($authors->isEmpty()) {
            $this->command->error('→ Posts: SKIPPED — no registrar or program head found.');
            return;
        }

        $categories = Category::query()->pluck('id');

        if ($categories->isEmpty()) {
            $this->command->error('→ Posts: SKIPPED — no categories found. Run CategorySeeder first.');
            return;
        }

        // Self-clearing: wipe existing posts so re-running produces a fresh batch.
        // If you want to preserve hand-curated posts, comment this block out
        // and use the "skip if exists" guard instead.
        if (Post::query()->exists()) {
            $this->command->warn('→ Posts: clearing existing rows before re-seeding.');
            Post::query()->delete();
        }

        $bar = $this->command->getOutput()->createProgressBar($this->count);
        $bar->start();

        for ($i = 0; $i < $this->count; $i++) {
            $author = $authors->random();

            // ~85% public · 15% draft
            $status = fake()->boolean(85) ? 'public' : 'draft';

            $title = $this->sampleTitle();

            Post::create([
                'user_id'     => $author->id,
                'title'       => $title,
                'slug'        => $this->uniqueSlug($title),
                'description' => $this->description(),
                'image'       => null, // seeded posts have no cover image
                'category_id' => $this->categoryIdFor($title, $categories),
                'status'      => $status,
                'attachments' => $this->attachments(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('  ✓ Posts: '.Post::count());
    }

    // =========================================================
    //  CATEGORY MATCHING
    // =========================================================

    /**
     * Map a title to the most relevant category by keyword.
     * Falls back to a random category when nothing matches.
     */
    protected function categoryIdFor(string $title, $categories): int
    {
        $lower = strtolower($title);

        $keywordMap = [
            'board'          => 'board-exam-results',
            'top notcher'    => 'board-exam-results',
            'scholarship'    => 'scholarships-grants',
            'grant'          => 'scholarships-grants',
            'career'         => 'career-opportunities',
            'job'            => 'career-opportunities',
            'internship'     => 'career-opportunities',
            'tracer'         => 'tracer-study',
            'homecoming'     => 'events',
            'reunion'        => 'events',
            'event'          => 'events',
            'achievement'    => 'alumni-achievements',
            'recognized'     => 'alumni-achievements',
            'award'          => 'alumni-achievements',
            'story'          => 'alumni-stories',
            'testimonial'    => 'alumni-stories',
            'announcement'   => 'announcements',
            'reminder'       => 'announcements',
            'update'         => 'announcements',
            'welcome'        => 'announcements',
        ];

        $slug = null;
        foreach ($keywordMap as $needle => $categorySlug) {
            if (str_contains($lower, $needle)) {
                $slug = $categorySlug;
                break;
            }
        }

        if ($slug) {
            $match = Category::where('cat_slug', $slug)->value('id');
            if ($match) {
                return $match;
            }
        }

        return $categories->random();
    }

    // =========================================================
    //  CONTENT
    // =========================================================

    protected function sampleTitle(): string
    {
        return fake()->randomElement([
            'Important Announcement: Alumni Tracer Study',
            'CSAV Welcomes New Program Head for Engineering',
            'Board Exam Results — Congratulations to Our Passers!',
            'Scholarship Grants Now Open for Alumni Dependents',
            'Updated Alumni ID Claiming Schedule',
            'CSAV Partners with Local Industries for Internship Program',
            'New Alumni Lounge Now Open at the Main Campus',
            'Reminder: Submit Your Tracer Study Form',
            'Alumni Achievement: Batch 2015 Graduate Recognized Nationally',
            'Upcoming Accreditation Visit — What Alumni Can Do to Help',
            'Career Opportunities for CSAV Graduates This Quarter',
            'Alumni Directory Update — Please Verify Your Details',
            'CSAV Library Now Offers Online Journals for Alumni',
            'Call for Alumni Volunteers: Outreach Program 2026',
            'CSAV Alumni Association Elects New Officers',
            'Graduate School Now Accepting Applications',
            'Guidelines for Requesting Official Transcripts (Alumni)',
            'Welcome Message from the New Registrar',
            'Alumni Feedback Survey — Your Voice Matters',
            'CSAV Foundation Launches Endowment Fund',
            'Three CSAV Graduates Top the 2026 Nursing Licensure Exam',
            'Alumni Story: From Victorias to Silicon Valley',
            'Save the Date: Grand Alumni Homecoming 2026',
            'New Scholarship Endowment Honors Late Professor',
            'Now Hiring: 20+ Companies Recruiting CSAV Graduates',
        ]);
    }

    protected function description(): string
    {
        $openers = [
            'We are pleased to share the following update with the CSAV alumni community.',
            'Please take a moment to read this important announcement from the alumni office.',
            'The following information has been released for all CSAV graduates.',
            'We would like to inform all alumni about the following development.',
        ];

        $mid = fake()->randomElement([
            'For more details, please visit the alumni portal or contact the registrar\'s office.',
            'Kindly check the attached document for complete guidelines and requirements.',
            'Should you have any questions, feel free to reach out to your batch representative.',
            'We appreciate your continued support and engagement with the college.',
        ]);

        return fake()->randomElement($openers)."\n\n".$mid;
    }

    /**
     * ~30% of posts carry 1–3 attachments.
     */
    protected function attachments(): ?array
    {
        if (! fake()->boolean(30)) {
            return null;
        }

        $files = [
            'guidelines-2026.pdf',
            'tracer-form.pdf',
            'alumni-id-requirements.pdf',
            'event-program.pdf',
            'scholarship-application.pdf',
            'career-fair-brochure.pdf',
            'alumni-survey.pdf',
        ];

        return collect(fake()->randomElements($files, fake()->numberBetween(1, 3)))->values()->all();
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}