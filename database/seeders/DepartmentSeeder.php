<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $logoUrl = 'CSAV-LOGO';

        $departments = [
            [
                'dept_name' => 'Department of Computer Studies',
                'dept_code' => 'DOCS',
                'dept_desc' => 'Covers software development, networking, cybersecurity, and information systems.',
            ],
            [
                'dept_name' => 'Business, Management and Accountancy Department',
                'dept_code' => 'BMAD',
                'dept_desc' => 'Programs in management, accountancy, marketing, and entrepreneurship.',
            ],
            [
                'dept_name' => 'Education Department',
                'dept_code' => 'EDUC',
                'dept_desc' => 'Training future educators for elementary, secondary, and special education.',
            ],
            [
                'dept_name' => 'Engineering Department',
                'dept_code' => 'ENGR',
                'dept_desc' => 'Engineering programs and research.',
            ],
            [
                'dept_name' => 'Criminal Justice Department',
                'dept_code' => 'CRIM',
                'dept_desc' => 'Law enforcement, criminal justice, and forensic science.',
            ],
            [
                'dept_name' => 'Arts and Sciences Department',
                'dept_code' => 'ARTS',
                'dept_desc' => 'Liberal arts, natural sciences, and general education programs.',
            ],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(
                ['dept_slug' => Str::slug($dept['dept_name'])],
                [
                    'dept_name'       => $dept['dept_name'],
                    'dept_code'       => $dept['dept_code'],
                    'dept_desc'       => $dept['dept_desc'],
                    'dept_logo'       => $logoUrl,
                    'program_head_id' => null,
                    'is_active'       => true,
                ]
            );
        }

        $this->command->info('Departments seeded: ' . Department::count());
    }
}