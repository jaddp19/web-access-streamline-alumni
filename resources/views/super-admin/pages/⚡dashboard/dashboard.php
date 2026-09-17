<?php

use App\Models\Batch;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    protected int $ttl = 300;

    /**
     * Selected batch ID. Null = "Overall" (all batches).
     */
    public ?int $selectedBatchId = null;

    public function updatedSelectedBatchId(): void
    {
        // Signal the frontend to re-init charts with new data
        $this->dispatch('batch-changed');
    }

    protected function cacheKey(string $prefix): string
    {
        return $prefix . '.' . ($this->selectedBatchId ?? 'all');
    }

    // ===== Batches for the selector =====

    #[Computed]
    public function batches()
    {
        return Cache::remember('analytics.batches_list', 3600, function () {
            return Batch::orderBy('batch_name', 'desc')
                ->get(['id', 'batch_name'])
                ->map(fn ($b) => ['id' => $b->id, 'batch_name' => $b->batch_name])
                ->toArray();
        });
    }

    // ===== Analytics =====

    #[Computed]
    public function alumniByDept()
    {
        $batchId = $this->selectedBatchId;

        return Cache::remember($this->cacheKey('analytics.alumni_by_dept'), $this->ttl, function () use ($batchId) {
            return DB::table('departments')
                ->join('courses', 'courses.department_id', '=', 'departments.id')
                ->join('student_course', 'courses.id', '=', 'student_course.course_id')
                ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
                ->where('departments.is_active', true)
                ->where('courses.is_active', true)
                ->when($batchId, fn ($q) => $q->where('user_profiles.batch_id', $batchId))
                ->select(
                    'departments.dept_name',
                    'departments.dept_code',
                    DB::raw('COUNT(DISTINCT user_profiles.user_id) as total')
                )
                ->groupBy('departments.id', 'departments.dept_name', 'departments.dept_code')
                ->orderBy('departments.dept_name')
                ->get()
                ->mapWithKeys(fn ($row) => [
                    $row->dept_code => [
                        'name'  => $row->dept_name,
                        'total' => $row->total,
                    ],
                ])
                ->toArray();
        });
    }

    #[Computed]
public function alumniByBatch()
{
    return Cache::remember('analytics.alumni_by_batch', $this->ttl, function () {
        return DB::table('user_profiles')
            ->join('batches', 'user_profiles.batch_id', '=', 'batches.id')
            ->select(
                'batches.id as batch_id',
                'batches.batch_name',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total')
            )
            ->groupBy('batches.id', 'batches.batch_name')
            ->orderBy('batches.batch_name', 'asc')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->batch_id => [
                    'batch_name' => $row->batch_name,
                    'total'      => $row->total,
                ],
            ])
            ->toArray();
    });
}

    #[Computed]
    public function courseAnalytics()
    {
        $batchId = $this->selectedBatchId;

        return Cache::remember($this->cacheKey('analytics.course'), $this->ttl, function () use ($batchId) {
            return DB::table('courses')
                ->join('student_course', 'courses.id', '=', 'student_course.course_id')
                ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
                ->leftJoin('tracer_studies', 'user_profiles.user_id', '=', 'tracer_studies.user_id')
                ->leftJoin('civil_status_employments', 'tracer_studies.id', '=', 'civil_status_employments.tracer_study_id')
                ->leftJoin('further_studies', 'tracer_studies.id', '=', 'further_studies.tracer_study_id')
                ->where('courses.is_active', true)
                ->when($batchId, fn ($q) => $q->where('user_profiles.batch_id', $batchId))
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
                        'course_title'       => $item->course_title,
                        'course_code'        => $item->course_code,
                        'total'              => $total,
                        'employed_rate'      => $total > 0 ? round(($item->employed_count / $total) * 100) : 0,
                        'related_rate'       => $total > 0 ? round(($item->related_count / $total) * 100) : 0,
                        'further_study_rate' => $total > 0 ? round(($item->further_study_count / $total) * 100) : 0,
                    ];
                })
                ->values()
                ->toArray();
        });
    }

    #[Computed]
    public function users()
    {
        return Cache::remember('analytics.users_total', $this->ttl, fn () => User::count());
    }

    #[Computed]
    public function alumni()
    {
        $batchId = $this->selectedBatchId;

        return Cache::remember($this->cacheKey('analytics.alumni_total'), $this->ttl, function () use ($batchId) {
            $query = User::role('alumni');

            if ($batchId) {
                $query->whereHas('userProfile', fn ($q) => $q->where('batch_id', $batchId));
            }

            return $query->count();
        });
    }

    #[Computed]
    public function programHeads()
    {
        return Cache::remember('analytics.program_heads', $this->ttl, fn () => User::role('program head')->count());
    }

    #[Computed]
    public function active()
    {
        $batchId = $this->selectedBatchId;

        return Cache::remember($this->cacheKey('analytics.active_alumni'), $this->ttl, function () use ($batchId) {
            return User::role('alumni')
                ->whereHas('userProfile', function ($q) use ($batchId) {
                    $q->where('is_verified', true);
                    if ($batchId) {
                        $q->where('batch_id', $batchId);
                    }
                })
                ->count();
        });
    }

    #[Computed]
    public function employmentStatusBreakdown()
    {
        return Cache::remember($this->cacheKey('analytics.employment_status'), $this->ttl, fn () =>
            $this->tracerBreakdown('employment_status')
        );
    }

    #[Computed]
    public function employmentTypeBreakdown()
    {
        return Cache::remember($this->cacheKey('analytics.employment_type'), $this->ttl, fn () =>
            $this->tracerBreakdown('employment_type')
        );
    }

    #[Computed]
    public function organizationTypeBreakdown()
    {
        return Cache::remember($this->cacheKey('analytics.organization_type'), $this->ttl, fn () =>
            $this->tracerBreakdown('organization_type')
        );
    }

    #[Computed]
    public function employmentAreaBreakdown()
    {
        return Cache::remember($this->cacheKey('analytics.employment_area'), $this->ttl, fn () =>
            $this->tracerBreakdown('employment_area')
        );
    }

    #[Computed]
    public function monthsToFirstJobBreakdown()
    {
        return Cache::remember($this->cacheKey('analytics.months_first_job'), $this->ttl, function () {
            $order = ['1-3-months', '4-6-months', 'more-than-6-months', 'more-than-1-year', 'not-yet-employed'];
            $data  = $this->tracerBreakdown('months_to_first_job');

            $sorted = [];
            foreach ($order as $key) {
                $sorted[$key] = (int) ($data[$key] ?? 0);
            }

            return $sorted;
        });
    }

    #[Computed]
    public function furtherStudiesRate()
    {
        $batchId = $this->selectedBatchId;

        return Cache::remember($this->cacheKey('analytics.further_studies_rate'), $this->ttl, function () use ($batchId) {
            $query = DB::table('further_studies');

            if ($batchId) {
                $query
                    ->join('tracer_studies', 'further_studies.tracer_study_id', '=', 'tracer_studies.id')
                    ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
                    ->where('user_profiles.batch_id', $batchId);
            }

            $data = $query
                ->select('is_pursued_further_studies', DB::raw('COUNT(*) as total'))
                ->groupBy('is_pursued_further_studies')
                ->pluck('total', 'is_pursued_further_studies')
                ->toArray();

            $yes   = (int) ($data[1] ?? $data['1'] ?? 0);
            $total = array_sum($data);

            return $total > 0 ? round(($yes / $total) * 100) : 0;
        });
    }

    protected function tracerBreakdown(string $column): array
    {
        $batchId = $this->selectedBatchId;

        $query = DB::table('civil_status_employments')
            ->whereNotNull($column)
            ->where($column, '!=', '');

        if ($batchId) {
            $query
                ->join('tracer_studies', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
                ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
                ->where('user_profiles.batch_id', $batchId);
        }

        return $query
            ->select($column, DB::raw('COUNT(*) as total'))
            ->groupBy($column)
            ->pluck('total', $column)
            ->toArray();
    }
};