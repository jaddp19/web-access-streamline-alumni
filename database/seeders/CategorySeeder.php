<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Alumni-appropriate categories.
     * Add or remove as your content mix evolves — the seeder is idempotent,
     * so re-running just syncs the list without duplicating.
     */
    protected array $categories = [
        [
            'cat_name' => 'Announcements',
            'cat_desc' => 'Official announcements from the registrar and program heads.',
        ],
        [
            'cat_name' => 'Events',
            'cat_desc' => 'Upcoming events, homecomings, reunions, and gatherings.',
        ],
        [
            'cat_name' => 'Board Exam Results',
            'cat_desc' => 'Board exam passers, top notchers, and licensure updates.',
        ],
        [
            'cat_name' => 'Career Opportunities',
            'cat_desc' => 'Job postings, internships, and career resources for alumni.',
        ],
        [
            'cat_name' => 'Alumni Achievements',
            'cat_desc' => 'Recognitions, awards, and milestones of CSAV graduates.',
        ],
        [
            'cat_name' => 'Scholarships & Grants',
            'cat_desc' => 'Scholarship programs, grants, and financial aid opportunities.',
        ],
        [
            'cat_name' => 'Tracer Study',
            'cat_desc' => 'Reminders and updates about the alumni tracer study.',
        ],
        [
            'cat_name' => 'Alumni Stories',
            'cat_desc' => 'Feature stories and testimonials from CSAV graduates.',
        ],
    ];

    public function run(): void
    {
        foreach ($this->categories as $category) {
            Category::updateOrCreate(
                ['cat_slug' => Str::slug($category['cat_name'])],
                [
                    'cat_name' => $category['cat_name'],
                    'cat_desc' => $category['cat_desc'],
                ]
            );
        }

        $this->command->info('  ✓ Categories: '.Category::count());
    }
}