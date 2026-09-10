<?php

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    #[Computed]
    public function alumniByDept()
    {
        return DB::table('courses')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->where('user_profiles.is_verified', true)
            ->select('courses.course_title', DB::raw('count(distinct user_profiles.user_id) as total'))
            ->groupBy('courses.id', 'courses.course_title')
            ->pluck('total', 'course_title')
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
            ->where('user_profiles.is_verified', true)
            ->select(
                'courses.course_title',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total_alumni'),
                DB::raw("SUM(CASE WHEN civil_status_employments.employment_status IN ('employed', 'self-employed') THEN 1 ELSE 0 END) as employed_count"),
                DB::raw("SUM(CASE WHEN civil_status_employments.employed_related_to_degree = 'yes' THEN 1 ELSE 0 END) as related_count"),
                DB::raw("SUM(CASE WHEN further_studies.is_pursued_further_studies = 1 THEN 1 ELSE 0 END) as further_study_count")
            )
            ->groupBy('courses.id', 'courses.course_title')
            ->get()
            ->map(function ($item) {
                $total = (int) $item->total_alumni;
                return [
                    'course_title' => $item->course_title,
                    'total' => $total,
                    'employed_rate' => $total > 0 ? round(($item->employed_count / $total) * 100) : 0,
                    'related_rate' => $total > 0 ? round(($item->related_count / $total) * 100) : 0,
                    'further_study_rate' => $total > 0 ? round(($item->further_study_count / $total) * 100) : 0,
                ];
            })
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
        return 0;
    }

    #[Computed]
    public function active()
    {
        $verifiedUserIds = UserProfile::where('is_verified', true)->pluck('user_id');

        return User::role('alumni')->whereIn('id', $verifiedUserIds)->count();
    }

    #[Computed]
    public function employmentTypeBreakdown()
    {
        return DB::table('civil_status_employments')
            ->join('tracer_studies', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
            ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->where('user_profiles.is_verified', true)
            ->whereNotNull('employment_type')
            ->select('employment_type', DB::raw('count(*) as total'))
            ->groupBy('employment_type')
            ->pluck('total', 'employment_type')
            ->toArray();
    }

    #[Computed]
    public function organizationTypeBreakdown()
    {
        return DB::table('civil_status_employments')
            ->join('tracer_studies', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
            ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->where('user_profiles.is_verified', true)
            ->whereNotNull('organization_type')
            ->select('organization_type', DB::raw('count(*) as total'))
            ->groupBy('organization_type')
            ->pluck('total', 'organization_type')
            ->toArray();
    }

    #[Computed]
    public function employmentAreaBreakdown()
    {
        return DB::table('civil_status_employments')
            ->join('tracer_studies', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
            ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->where('user_profiles.is_verified', true)
            ->whereNotNull('employment_area')
            ->select('employment_area', DB::raw('count(*) as total'))
            ->groupBy('employment_area')
            ->pluck('total', 'employment_area')
            ->toArray();
    }

    #[Computed]
    public function monthsToFirstJobBreakdown()
    {
        $order = ['1-3-months', '4-6-months', 'more-than-6-months', 'more-than-1-year', 'not-yet-employed'];

        $data = DB::table('civil_status_employments')
            ->join('tracer_studies', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
            ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->where('user_profiles.is_verified', true)
            ->whereNotNull('months_to_first_job')
            ->select('months_to_first_job', DB::raw('count(*) as total'))
            ->groupBy('months_to_first_job')
            ->pluck('total', 'months_to_first_job')
            ->toArray();

        $sorted = [];
        foreach ($order as $key) {
            $sorted[$key] = $data[$key] ?? 0;
        }

        return $sorted;
    }

    #[Computed]
    public function furtherStudiesRate()
    {
        $data = DB::table('further_studies')
            ->join('tracer_studies', 'further_studies.tracer_study_id', '=', 'tracer_studies.id')
            ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->where('user_profiles.is_verified', true)
            ->select('is_pursued_further_studies', DB::raw('count(*) as total'))
            ->groupBy('is_pursued_further_studies')
            ->pluck('total', 'is_pursued_further_studies')
            ->toArray();

        $total = array_sum($data);
        return $total > 0 ? round((($data[1] ?? 0) / $total) * 100) : 0;
    }
};
