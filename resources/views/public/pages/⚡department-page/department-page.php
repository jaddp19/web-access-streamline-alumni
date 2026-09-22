<?php

use App\Models\Department;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    // =========================================================
    //  DEPARTMENTS — cached as plain arrays for 10 minutes
    // =========================================================

    #[Computed]
    public function departments(): array
    {
        $cacheKey = 'landing:departments:v1';
        $cached   = Cache::get($cacheKey);

        if (! is_array($cached)) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if ($cached === null) {
            $cached = Department::query()
                ->where('is_active', true)
                ->select('id', 'dept_name', 'dept_desc', 'dept_logo')
                ->selectSub(function ($q) {
                    $q->from('student_course')
                      ->join('courses', 'courses.id', '=', 'student_course.course_id')
                      ->whereColumn('courses.department_id', 'departments.id')
                      ->where('courses.is_active', true)
                      ->selectRaw('COUNT(DISTINCT student_course.user_profile_id)');
                }, 'students_count')
                ->with([
                    'courses' => fn ($q) => $q
                        ->where('is_active', true)
                        ->select('id', 'department_id', 'course_title')
                        ->orderBy('course_title'),
                ])
                ->orderBy('dept_name')
                ->get()
                ->map(fn ($dept) => [
                    'id'             => (int) $dept->id,
                    'dept_name'      => (string) $dept->dept_name,
                    'dept_desc'      => $dept->dept_desc ? (string) $dept->dept_desc : null,
                    'dept_logo'      => $dept->dept_logo ? (string) $dept->dept_logo : null,
                    'students_count' => (int) $dept->students_count,
                    'courses'        => $dept->courses
                        ->map(fn ($c) => [
                            'id'           => (int) $c->id,
                            'course_title' => (string) $c->course_title,
                        ])
                        ->all(),
                ])
                ->all();

            Cache::put($cacheKey, $cached, now()->addMinutes(10));
        }

        return $cached;
    }
};