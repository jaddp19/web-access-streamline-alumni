<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    #[Computed]
    public function alumniByDept()
    {
        return DB::table('departments')
            ->join('courses', 'courses.department_id', '=', 'departments.id')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->select('departments.dept_name', DB::raw('COUNT(DISTINCT user_profiles.user_id) as total'))
            ->groupBy('departments.id', 'departments.dept_name')
            ->orderBy('departments.dept_name')
            ->pluck('total', 'dept_name')
            ->toArray();
    }

    #[Computed]
    public function courseAnalytics()
    {
        return DB::table('courses')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->leftJoin('tracer_studies', 'user_profiles.user_id', '=', 'tracer_studies.user_id')
            ->leftJoin('civil_status_employments', 'tracer_studies.id', '=', 'civil_status_employments.tracer_study_id')
            ->leftJoin('further_studies', 'tracer_studies.id', '=', 'further_studies.tracer_study_id')
            ->select(
                'courses.course_title',
                'courses.course_code',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total_alumni'),
                DB::raw("COUNT(DISTINCT CASE WHEN civil_status_employments.employment_status IN ('employed', 'self-employed') THEN user_profiles.user_id END) as employed_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN civil_status_employments.employed_related_to_degree = 'yes' THEN user_profiles.user_id END) as related_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN further_studies.is_pursued_further_studies = 1 THEN user_profiles.user_id END) as further_study_count")
            )
            ->groupBy('courses.id', 'courses.course_code', 'courses.course_title')
            ->orderBy('courses.course_title')
            ->get()
            ->map(function ($item) {
                $total = (int) $item->total_alumni;

                return [
                    'course_title' => $item->course_title,
                    'course_code' => $item->course_code,
                    'total' => $total,
                    'employed_rate' => $total > 0 ? round(($item->employed_count / $total) * 100) : 0,
                    'related_rate' => $total > 0 ? round(($item->related_count / $total) * 100) : 0,
                    'further_study_rate' => $total > 0 ? round(($item->further_study_count / $total) * 100) : 0,
                ];
            })
            ->values()
            ->toArray();
    }

    #[Computed]
    public function users()
    {
        return User::count();
    }

    #[Computed]
    public function alumni()
    {
        return User::role('alumni')->count();
    }

    #[Computed]
    public function programHeads()
    {
        return User::role('program head')->count();
    }

    #[Computed]
    public function active()
    {
        return User::role('alumni')
            ->whereHas('userProfile', fn ($q) => $q->where('is_verified', true))
            ->count();
    }

    #[Computed]
    public function employmentStatusBreakdown()
    {
        return $this->tracerBreakdown('employment_status');
    }

    #[Computed]
    public function employmentTypeBreakdown()
    {
        return $this->tracerBreakdown('employment_type');
    }

    #[Computed]
    public function organizationTypeBreakdown()
    {
        return $this->tracerBreakdown('organization_type');
    }

    #[Computed]
    public function employmentAreaBreakdown()
    {
        return $this->tracerBreakdown('employment_area');
    }

    #[Computed]
    public function monthsToFirstJobBreakdown()
    {
        $order = ['1-3-months', '4-6-months', 'more-than-6-months', 'more-than-1-year', 'not-yet-employed'];
        $data = $this->tracerBreakdown('months_to_first_job');

        $sorted = [];
        foreach ($order as $key) {
            $sorted[$key] = (int) ($data[$key] ?? 0);
        }

        return $sorted;
    }

    #[Computed]
    public function furtherStudiesRate()
    {
        $data = DB::table('further_studies')
            ->select('is_pursued_further_studies', DB::raw('COUNT(*) as total'))
            ->groupBy('is_pursued_further_studies')
            ->pluck('total', 'is_pursued_further_studies')
            ->toArray();

        $yes = (int) ($data[1] ?? $data['1'] ?? 0);
        $total = array_sum($data);

        return $total > 0 ? round(($yes / $total) * 100) : 0;
    }

    protected function tracerBreakdown(string $column): array
    {
        return DB::table('civil_status_employments')
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->select($column, DB::raw('COUNT(*) as total'))
            ->groupBy($column)
            ->pluck('total', $column)
            ->toArray();
    }
};