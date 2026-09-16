<?php

use App\Models\Department;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function departments()
    {
        return Department::query()
            ->select('departments.*')
            ->selectSub(function ($q) {
                $q->from('student_course')
                  ->join('courses', 'courses.id', '=', 'student_course.course_id')
                  ->whereColumn('courses.department_id', 'departments.id')
                  ->selectRaw('COUNT(DISTINCT student_course.user_profile_id)');
            }, 'students_count')
            ->with(['courses' => fn ($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('dept_name')
            ->get();
    }
};