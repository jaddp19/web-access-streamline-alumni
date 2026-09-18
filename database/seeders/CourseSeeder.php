<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Map course → department slug (must match DepartmentSeeder)
        $courses = [

            // ===== Computer Studies =====
            
            [
                'course_title'   => 'Bachelor of Science in Information Systems',
                'course_code'    => 'BSIS',
                'course_type'    => 'non-board',
                'department'     => 'Department of Computer Studies',
                'course_desc'    => 'Blends business processes with information systems design and management.',
            ],
            [
                'course_title'   => 'Bachelor of Science in Computer Science',
                'course_code'    => 'BSCS',
                'course_type'    => 'non-board',
                'department'     => 'Department of Computer Studies',
                'course_desc'    => 'Theoretical and applied computing, algorithms, and software engineering.',
            ],

            // ===== Business Administration =====
            [
                'course_title'   => 'Bachelor of Science in Agribusiness',
                'course_code'    => 'BSAB',
                'course_type'    => 'non-board',
                'department'     => 'Business, Management and Accountancy Department',
                'course_desc'    => 'Major in Management, Marketing, or Financial Management.',
            ],
            [
                'course_title'   => 'Bachelor of Science in Office Administration',
                'course_code'    => 'BSOA',
                'course_type'    => 'non-board',
                'department'     => 'Business, Management and Accountancy Department',
                'course_desc'    => 'Focuses on administrative procedures and office management.',
            ],
            [
                'course_title'   => 'Bachelor of Science in Accountancy',
                'course_code'    => 'BSA',
                'course_type'    => 'board',
                'department'     => 'Business, Management and Accountancy Department',
                'course_desc'    => 'Prepares students for the CPA licensure examination and accounting practice.',
            ],
            [
                'course_title'   => 'Bachelor of Science in Tourism Management',
                'course_code'    => 'BSTM',
                'course_type'    => 'non-board',
                'department'     => 'Business, Management and Accountancy Department',
                'course_desc'    => 'Travel operations, destination marketing, and tourism planning.',
            ],

            // ===== Teacher Education =====
            [
                'course_title'   => 'Bachelor of Elementary Education',
                'course_code'    => 'BEEd',
                'course_type'    => 'board',
                'department'     => 'Education Department',
                'course_desc'    => 'Prepares generalist teachers for elementary grades.',
            ],
            [
                'course_title'   => 'Teacher Certificate Program',
                'course_code'    => 'TCP',
                'course_type'    => 'non-board',
                'department'     => 'Education Department',
                'course_desc'    => '18-unit professional education course designed for college graduates of non-education degrees who want to become licensed teachers',
            ],
            [
                'course_title'   => 'Bachelor of Science in Physical Education',
                'course_code'    => 'BPEd',
                'course_type'    => 'board',
                'department'     => 'Education Department',
                'course_desc'    => 'Education in physical fitness, sports, and health education for future PE teachers.',
            ],

            // ===== Criminology =====
            [
                'course_title'   => 'Bachelor of Science in Criminology',
                'course_code'    => 'BSCrim',
                'course_type'    => 'board',
                'department'     => 'Criminal Justice Department',
                'course_desc'    => 'Prepares students for law enforcement and the criminologist licensure exam.',
            ],

            // ===== Arts & Sciences =====
            [
                'course_title'   => 'Bachelor of Science in Social Work',
                'course_code'    => 'BSSW',
                'course_type'    => 'board',
                'department'     => 'Arts & Sciences Department',
                'course_desc'    => 'Social services, community development, and human welfare.',
            ],
            [
                'course_title'   => 'Bachelor of Science in Psychology',
                'course_code'    => 'BSPsych',
                'course_type'    => 'board',
                'department'     => 'Arts & Sciences Department',
                'course_desc'    => 'Human behavior, mental processes, and applied psychology.',
            ],

            // ===== Engineering =====
            [
                'course_title'   => 'Bachelor of Science in Civil Engineering',
                'course_code'    => 'BSCE',
                'course_type'    => 'board',
                'department'     => 'Engineering Department',
                'course_desc'    => 'Design, construction, and maintenance of infrastructure and buildings.',
            ],
            [
                'course_title'   => 'Bachelor of Science in Mechanical Engineering',
                'course_code'    => 'BSME',
                'course_type'    => 'board',
                'department'     => 'Engineering Department',
                'course_desc'    => 'Mechanical systems, thermodynamics, and manufacturing processes.',
            ],
        ];

        $departments = Department::pluck('id', 'dept_name');

        foreach ($courses as $course) {
            $departmentId = $departments[$course['department']] ?? null;

            if (! $departmentId) {
                $this->command->warn("Skipped {$course['course_code']}: department '{$course['department']}' not found.");
                continue;
            }

            Course::updateOrCreate(
                ['course_slug' => Str::slug($course['course_title'])],
                [
                    'course_title'   => $course['course_title'],
                    'course_code'    => $course['course_code'],
                    'course_type'    => $course['course_type'],
                    'course_desc'    => $course['course_desc'],
                    'department_id'  => $departmentId,
                    'is_active'      => true,
                ]
            );
        }

        $this->command->info('Courses seeded: ' . Course::count());
    }
}