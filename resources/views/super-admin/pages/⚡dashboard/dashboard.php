<?php

use App\Models\Batch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public ?int $selectedBatchId = null;

    public function updatedSelectedBatchId(): void
    {
        $this->dispatch('batch-changed');
    }

    // ===== Batches for the selector =====

    #[Computed]
    public function batches()
    {
        return Batch::orderBy('batch_name', 'desc')
            ->get(['id', 'batch_name'])
            ->map(fn ($b) => ['id' => $b->id, 'batch_name' => $b->batch_name])
            ->toArray();
    }

    // ===== Analytics (no cache — always fresh) =====

    #[Computed]
    public function alumniByDept()
    {
        return DB::table('departments')
            ->join('courses', 'courses.department_id', '=', 'departments.id')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->where('departments.is_active', true)
            ->where('courses.is_active', true)
            ->when($this->selectedBatchId, fn ($q) => $q->where('user_profiles.batch_id', $this->selectedBatchId))
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
                    'total' => (int) $row->total,
                ],
            ])
            ->toArray();
    }

    #[Computed]
    public function alumniByBatch()
    {
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
                    'total'      => (int) $row->total,
                ],
            ])
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
            ->where('courses.is_active', true)
            ->when($this->selectedBatchId, fn ($q) => $q->where('user_profiles.batch_id', $this->selectedBatchId))
            ->select(
                'courses.course_title',
                'courses.course_code',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total_alumni'),
                DB::raw("SUM(CASE WHEN civil_status_employments.employment_status IN ('employed', 'self-employed') THEN 1 ELSE 0 END) as employed_count"),
                DB::raw("SUM(CASE WHEN civil_status_employments.employed_related_to_degree = 'yes' THEN 1 ELSE 0 END) as related_count"),
                DB::raw("SUM(CASE WHEN further_studies.is_pursued_further_studies = 1 THEN 1 ELSE 0 END) as further_study_count")
            )
            ->groupBy('courses.id', 'courses.course_code', 'courses.course_title')
            ->orderBy('courses.course_title')
            ->get()
            ->map(function ($item) {
                $total = max((int) $item->total_alumni, 1);

                return [
                    'course_title'       => $item->course_title,
                    'course_code'        => $item->course_code,
                    'total'              => (int) $item->total_alumni,
                    'employed_rate'      => round(($item->employed_count / $total) * 100),
                    'related_rate'       => round(($item->related_count / $total) * 100),
                    'further_study_rate' => round(($item->further_study_count / $total) * 100),
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
        $query = User::role('alumni');

        if ($this->selectedBatchId) {
            $query->whereHas('userProfile', fn ($q) => $q->where('batch_id', $this->selectedBatchId));
        }

        return $query->count();
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
            ->whereHas('userProfile', function ($q) {
                $q->where('is_verified', true);
                if ($this->selectedBatchId) {
                    $q->where('batch_id', $this->selectedBatchId);
                }
            })
            ->count();
    }

    // ===== Tracer breakdowns (single query, split in PHP) =====

    #[Computed]
    public function tracerBreakdowns(): array
    {
        $query = DB::table('civil_status_employments')
            ->select([
                'employment_status',
                'employment_type',
                'organization_type',
                'employment_area',
                'months_to_first_job',
            ]);

        if ($this->selectedBatchId) {
            $query
                ->join('tracer_studies', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
                ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
                ->where('user_profiles.batch_id', $this->selectedBatchId);
        }

        $rows = $query->get();

        $out = [
            'employment_status'   => [],
            'employment_type'     => [],
            'organization_type'   => [],
            'employment_area'     => [],
            'months_to_first_job' => [],
        ];

        foreach ($rows as $row) {
            foreach ($out as $col => $_) {
                $val = $row->$col ?? null;
                if ($val === null || $val === '') {
                    continue;
                }
                $out[$col][$val] = ($out[$col][$val] ?? 0) + 1;
            }
        }

        $order        = ['1-3-months', '4-6-months', 'more-than-6-months', 'more-than-1-year', 'not-yet-employed'];
        $sortedMonths = [];
        foreach ($order as $key) {
            $sortedMonths[$key] = (int) ($out['months_to_first_job'][$key] ?? 0);
        }
        $out['months_to_first_job'] = $sortedMonths;

        return $out;
    }

    #[Computed]
    public function employmentStatusBreakdown()
    {
        return $this->tracerBreakdowns['employment_status'];
    }

    #[Computed]
    public function employmentTypeBreakdown()
    {
        return $this->tracerBreakdowns['employment_type'];
    }

    #[Computed]
    public function organizationTypeBreakdown()
    {
        return $this->tracerBreakdowns['organization_type'];
    }

    #[Computed]
    public function employmentAreaBreakdown()
    {
        return $this->tracerBreakdowns['employment_area'];
    }

    #[Computed]
    public function monthsToFirstJobBreakdown()
    {
        return $this->tracerBreakdowns['months_to_first_job'];
    }

    // ===== Further studies rate =====

    #[Computed]
    public function furtherStudiesRate()
    {
        $query = DB::table('further_studies');

        if ($this->selectedBatchId) {
            $query
                ->join('tracer_studies', 'further_studies.tracer_study_id', '=', 'tracer_studies.id')
                ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
                ->where('user_profiles.batch_id', $this->selectedBatchId);
        }

        $data = $query
            ->select('is_pursued_further_studies', DB::raw('COUNT(*) as total'))
            ->groupBy('is_pursued_further_studies')
            ->pluck('total', 'is_pursued_further_studies')
            ->toArray();

        $yes   = (int) ($data[1] ?? $data['1'] ?? 0);
        $total = array_sum($data);

        return $total > 0 ? round(($yes / $total) * 100) : 0;
    }

    /**
     * Manual refresh — triggered from a button in the blade.
     * Since we no longer cache, this simply re-renders.
     */
    public function refreshAnalytics(): void
    {
        $this->dispatch('batch-changed');
    }
};