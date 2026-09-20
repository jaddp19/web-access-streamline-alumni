<?php

use App\Models\Batch;
use App\Models\CivilStatusEmployment;
use App\Models\Course;
use App\Models\User;
use App\Models\UserProfile;
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
            ->map(fn($b) => ['id' => $b->id, 'batch_name' => $b->batch_name])
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
            ->when($this->selectedBatchId, fn($q) => $q->where('user_profiles.batch_id', $this->selectedBatchId))
            ->select(
                'departments.dept_name',
                'departments.dept_code',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total')
            )
            ->groupBy('departments.id', 'departments.dept_name', 'departments.dept_code')
            ->orderBy('departments.dept_name')
            ->get()
            ->mapWithKeys(fn($row) => [
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
        $alumniUserIds = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'alumni')
            ->where('model_has_roles.model_type', User::class)
            ->pluck('model_has_roles.model_id');

        return DB::table('user_profiles')
            ->join('batches', 'user_profiles.batch_id', '=', 'batches.id')
            ->whereIn('user_profiles.user_id', $alumniUserIds)
            ->select(
                'batches.id as batch_id',
                'batches.batch_name',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total')
            )
            ->groupBy('batches.id', 'batches.batch_name')
            ->orderBy('batches.batch_name', 'asc')
            ->get()
            ->mapWithKeys(fn($row) => [
                $row->batch_id => [
                    'batch_name' => $row->batch_name,
                    'total'      => (int) $row->total,
                ],
            ])
            ->toArray();
    }

    #[Computed]
    public function courseAnalytics(): array
    {
        $courses = Course::query()
            ->where('is_active', true)
            ->get(['id', 'course_code']);

        $out = [];
        foreach ($courses as $course) {
            // Alumni IDs for this course (and batch if filtered)
            $alumniIds = UserProfile::whereHas('courses', fn($q) => $q->where('course_id', $course->id))
                ->when($this->selectedBatchId, fn($q) => $q->where('batch_id', $this->selectedBatchId))
                ->pluck('user_id');

            if ($alumniIds->isEmpty()) continue;

            // Only alumni who are EMPLOYED count toward alignment
            $employedBase = fn() => CivilStatusEmployment::whereHas(
                'tracerStudy',
                fn($q) => $q->whereIn('user_id', $alumniIds)
            )
                ->where('employment_status', 'employed');

            $employedTotal = $employedBase()->count();

            if ($employedTotal === 0) continue;

            $related = $employedBase()
                ->whereIn('employed_related_to_degree', ['yes', 'partially-related'])
                ->count();

            $out[] = [
                'course_code'  => $course->course_code,
                'related_rate' => round(($related / $employedTotal) * 100),
            ];
        }

        return $out;
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
            $query->whereHas('userProfile', fn($q) => $q->where('batch_id', $this->selectedBatchId));
        }

        return $query->count();
    }

    #[Computed]
    public function programHeads()
    {
        return User::role('program head')->count();
    }

    #[Computed]
    public function courses()
    {
        return Course::where('is_active', true)->count();
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

        $order        = ['1-3-months', '4-6-months', 'more-than-6-months', 'more-than-1-year'];
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
