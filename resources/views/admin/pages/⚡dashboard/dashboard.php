<?php

use App\Models\Batch;
use App\Models\CivilStatusEmployment;
use App\Models\Course;
use App\Models\Department;
use App\Models\FurtherStudy;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.app-admin')] class extends Component
{
    public $selectedBatchId = '';

    /**
     * null   → registrar (global)
     * int    → program head with a dept (scoped)
     * false  → program head with NO dept (sees nothing)
     */
    protected function scopeDepartmentId(): int|false|null
    {
        $user = Auth::user();
        if (! $user) return false;
        if ($user->hasRole('registrar')) return null;

        $dept = Department::where('program_head_id', $user->id)->first();
        return $dept?->id ?? false;
    }

    /**
     * Reusable alumni query builder with dept + batch scoping.
     */
    protected function alumniQuery()
    {
        $scope = $this->scopeDepartmentId();
        if ($scope === false) return null;

        return User::role('alumni')
            ->when(is_int($scope), function ($q) use ($scope) {
                $q->whereHas('userProfile.courses', fn($c) => $c->where('department_id', $scope));
            })
            ->when($this->selectedBatchId, function ($q) {
                $q->whereHas('userProfile', fn($p) => $p->where('batch_id', $this->selectedBatchId));
            });
    }

    #[Computed]
    public function courses(): int
    {
        $scope = $this->scopeDepartmentId();
        if ($scope === false) return 0;

        return Course::query()
            ->when(is_int($scope), fn($q) => $q->where('department_id', $scope))
            ->count();
    }

    #[Computed]
    public function isRegistrar(): bool
    {
        return Auth::user()?->hasRole('registrar') ?? false;
    }

    #[Computed]
    public function boardPassers(): int
    {
        $scope = $this->scopeDepartmentId();
        if ($scope === false) return 0;

        return UserProfile::query()
            ->where('is_verified', true)
            // Only board-program alumni can be board passers
            ->whereHas('courses', fn($c) => $c->where('course_type', 'board'))
            // Scoped to the program head's department
            ->when(
                is_int($scope),
                fn($q) =>
                $q->whereHas('courses', fn($c) => $c->where('department_id', $scope))
            )
            ->when(
                $this->selectedBatchId,
                fn($q) =>
                $q->where('batch_id', $this->selectedBatchId)
            )
            ->count();
    }

    #[Computed]
    public function pendingVerification(): int
    {
        $scope = $this->scopeDepartmentId();
        if ($scope === false) return 0;

        return UserProfile::query()
            ->where('is_verified', false)
            // Must have a board course in student_course
            ->whereHas('courses', fn($c) => $c->where('course_type', 'board'))
            // Scoped to the program head's department
            ->when(
                is_int($scope),
                fn($q) =>
                $q->whereHas('courses', fn($c) => $c->where('department_id', $scope))
            )
            ->when(
                $this->selectedBatchId,
                fn($q) =>
                $q->where('batch_id', $this->selectedBatchId)
            )
            ->count();
    }

    #[Computed]
    public function hasNoDepartment(): bool
    {
        return $this->scopeDepartmentId() === false;
    }

    #[Computed]
    public function myDepartmentName(): ?string
    {
        $id = $this->scopeDepartmentId();
        return is_int($id) ? Department::find($id)?->dept_name : null;
    }

    #[Computed]
    public function batches(): array
    {
        $scope = $this->scopeDepartmentId();
        if ($scope === false) return [];

        return Batch::query()
            ->when(is_int($scope), function ($q) use ($scope) {
                $q->whereHas('userProfiles', function ($p) use ($scope) {
                    $p->whereHas('user', fn($u) => $u->role('alumni'))
                        ->whereHas('courses', fn($c) => $c->where('department_id', $scope));
                });
            })
            ->orderByDesc('batch_name')
            ->get(['id', 'batch_name'])
            ->map(fn($b) => ['id' => $b->id, 'batch_name' => $b->batch_name])
            ->toArray();
    }

    // ========== TOP STAT CARDS ==========

    #[Computed]
    public function users(): int
    {
        $scope = $this->scopeDepartmentId();
        if ($scope === false) return 0;
        if (is_int($scope)) {
            return User::whereHas('userProfile.courses', fn($q) => $q->where('department_id', $scope))
                ->count();
        }
        return User::count();
    }

    #[Computed]
    public function active(): int
    {
        $q = $this->alumniQuery();
        if (! $q) return 0;
        return $q->whereHas('userProfile', fn($p) => $p->where('is_verified', true))->count();
    }

    #[Computed]
    public function alumni(): int
    {
        $q = $this->alumniQuery();
        if (! $q) return 0;
        return $q->count();
    }

    #[Computed]
    public function programHeads(): int
    {
        $scope = $this->scopeDepartmentId();
        if ($scope === false) return 0;
        if (is_int($scope)) {
            return User::role('program head')
                ->whereHas('department', fn($q) => $q->where('id', $scope))
                ->count();
        }
        return User::role('program head')->count();
    }

    // ========== CHART DATA ==========

    #[Computed]
    public function alumniByDept(): array
    {
        $scope = $this->scopeDepartmentId();
        if ($scope === false) return [];

        $departments = Department::query()
            ->when(is_int($scope), fn($q) => $q->where('id', $scope))
            ->get();

        $out = [];
        foreach ($departments as $dept) {
            $count = User::role('alumni')
                ->when($this->selectedBatchId, fn($q) => $q->whereHas('userProfile', fn($p) => $p->where('batch_id', $this->selectedBatchId)))
                ->whereHas('userProfile.courses', fn($c) => $c->where('department_id', $dept->id))
                ->count();

            $out[$dept->dept_code ?: ('DEPT-' . $dept->id)] = [
                'name'  => $dept->dept_name,
                'total' => $count,
            ];
        }
        return $out;
    }

    #[Computed]
    public function alumniByBatch(): array
    {
        $scope = $this->scopeDepartmentId();
        if ($scope === false) return [];

        $out = [];
        foreach ($this->batches as $batch) {
            $count = User::role('alumni')
                ->whereHas('userProfile', fn($p) => $p->where('batch_id', $batch['id']))
                ->when(is_int($scope), fn($q) => $q->whereHas('userProfile.courses', fn($c) => $c->where('department_id', $scope)))
                ->count();

            $out[(string) $batch['id']] = [
                'batch_name' => $batch['batch_name'],
                'total'      => $count,
            ];
        }
        return $out;
    }

    #[Computed]
    public function courseAnalytics(): array
    {
        $scope = $this->scopeDepartmentId();
        if ($scope === false) return [];

        $courses = Course::query()
            ->when(is_int($scope), fn($q) => $q->where('department_id', $scope))
            ->get(['id', 'course_code']);

        $out = [];
        foreach ($courses as $course) {
            $alumniIds = UserProfile::whereHas('courses', fn($q) => $q->where('course_id', $course->id))
                ->when($this->selectedBatchId, fn($q) => $q->where('batch_id', $this->selectedBatchId))
                ->pluck('user_id');

            $total = $alumniIds->count();
            if ($total === 0) continue;

            $related = CivilStatusEmployment::whereHas('tracerStudy', fn($q) => $q->whereIn('user_id', $alumniIds))
                ->whereIn('employed_related_to_degree', ['yes', 'partially-related'])
                ->count();

            $out[] = [
                'course_code'  => $course->course_code,
                'related_rate' => round(($related / $total) * 100),
            ];
        }
        return $out;
    }

    #[Computed]
    public function furtherStudiesRate(): int
    {
        $q = $this->alumniQuery();
        if (! $q) return 0;

        $ids = $q->pluck('id');
        $total = $ids->count();
        if ($total === 0) return 0;

        $pursued = FurtherStudy::whereHas('tracerStudy', fn($q) => $q->whereIn('user_id', $ids))
            ->where('is_pursued_further_studies', true)
            ->count();

        return (int) round(($pursued / $total) * 100);
    }

    #[Computed]
    public function employmentStatusBreakdown(): array
    {
        $q = $this->alumniQuery();
        if (! $q) return [];
        $ids = $q->pluck('id');

        return CivilStatusEmployment::whereHas('tracerStudy', fn($q) => $q->whereIn('user_id', $ids))
            ->whereNotNull('employment_status')
            ->select('employment_status', DB::raw('count(*) as total'))
            ->groupBy('employment_status')
            ->pluck('total', 'employment_status')
            ->toArray();
    }

    #[Computed]
    public function employmentTypeBreakdown(): array
    {
        $q = $this->alumniQuery();
        if (! $q) return [];
        $ids = $q->pluck('id');

        return CivilStatusEmployment::whereHas('tracerStudy', fn($q) => $q->whereIn('user_id', $ids))
            ->whereNotNull('employment_type')
            ->select('employment_type', DB::raw('count(*) as total'))
            ->groupBy('employment_type')
            ->pluck('total', 'employment_type')
            ->toArray();
    }

    #[Computed]
    public function organizationTypeBreakdown(): array
    {
        $q = $this->alumniQuery();
        if (! $q) return [];
        $ids = $q->pluck('id');

        return CivilStatusEmployment::whereHas('tracerStudy', fn($q) => $q->whereIn('user_id', $ids))
            ->whereNotNull('organization_type')
            ->select('organization_type', DB::raw('count(*) as total'))
            ->groupBy('organization_type')
            ->pluck('total', 'organization_type')
            ->toArray();
    }

    #[Computed]
    public function employmentAreaBreakdown(): array
    {
        $q = $this->alumniQuery();
        if (! $q) return [];
        $ids = $q->pluck('id');

        return CivilStatusEmployment::whereHas('tracerStudy', fn($q) => $q->whereIn('user_id', $ids))
            ->whereNotNull('employment_area')
            ->select('employment_area', DB::raw('count(*) as total'))
            ->groupBy('employment_area')
            ->pluck('total', 'employment_area')
            ->toArray();
    }

    #[Computed]
    public function monthsToFirstJobBreakdown(): array
    {
        $q = $this->alumniQuery();
        if (! $q) return [];
        $ids = $q->pluck('id');

        return CivilStatusEmployment::whereHas('tracerStudy', fn($q) => $q->whereIn('user_id', $ids))
            ->whereNotNull('months_to_first_job')
            ->select('months_to_first_job', DB::raw('count(*) as total'))
            ->groupBy('months_to_first_job')
            ->pluck('total', 'months_to_first_job')
            ->toArray();
    }

    // ========== NEW ANALYTICS ==========

    #[Computed]
    public function genderBreakdown(): array
    {
        $q = $this->alumniQuery();
        if (! $q) return [];

        return $q->with('userProfile:id,user_id,location')
            ->get()
            ->map(fn($u) => $u->userProfile?->location['gender'] ?? null)
            ->filter()
            ->map(fn($g) => ucfirst($g))
            ->countBy()
            ->toArray();
    }

    #[Computed]
    public function civilStatusBreakdown(): array
    {
        $q = $this->alumniQuery();
        if (! $q) return [];
        $ids = $q->pluck('id');

        return CivilStatusEmployment::whereHas('tracerStudy', fn($q) => $q->whereIn('user_id', $ids))
            ->whereNotNull('civil_status')
            ->select('civil_status', DB::raw('count(*) as total'))
            ->groupBy('civil_status')
            ->pluck('total', 'civil_status')
            ->toArray();
    }

    #[Computed]
    public function furtherStudiesLevelBreakdown(): array
    {
        $q = $this->alumniQuery();
        if (! $q) return [];
        $ids = $q->pluck('id');

        return FurtherStudy::whereHas('tracerStudy', fn($q) => $q->whereIn('user_id', $ids))
            ->where('is_pursued_further_studies', true)
            ->whereNotNull('level_of_study')
            ->select('level_of_study', DB::raw('count(*) as total'))
            ->groupBy('level_of_study')
            ->pluck('total', 'level_of_study')
            ->toArray();
    }

    #[Computed]
    public function topEmployers(): array
    {
        $q = $this->alumniQuery();
        if (! $q) return [];
        $ids = $q->pluck('id');

        return WorkHistory::whereIn('user_id', $ids)
            ->whereNotNull('company_id')
            ->with('company:id,company_name')
            ->get()
            ->groupBy(fn($w) => $w->company?->company_name ?? 'Unknown')
            ->map->count()
            ->sortDesc()
            ->take(8)
            ->toArray();
    }

    #[Computed]
    public function jobAlignmentBreakdown(): array
    {
        $q = $this->alumniQuery();
        if (! $q) return [];
        $ids = $q->pluck('id');

        return CivilStatusEmployment::whereHas('tracerStudy', fn($q) => $q->whereIn('user_id', $ids))
            ->whereNotNull('employed_related_to_degree')
            ->select('employed_related_to_degree', DB::raw('count(*) as total'))
            ->groupBy('employed_related_to_degree')
            ->pluck('total', 'employed_related_to_degree')
            ->toArray();
    }

    #[Computed]
    public function boardExamBreakdown(): array
    {
        $q = $this->alumniQuery();
        if (! $q) return [];

        $profiles = $q->with('userProfile:id,user_id,board_taken,board_rate')
            ->get()
            ->pluck('userProfile')
            ->filter(fn($p) => $p && filled($p->board_taken));

        $taken = $profiles->count();
        $passed = $profiles->filter(fn($p) => is_numeric($p->board_rate) && (float) $p->board_rate >= 75)->count();

        return [
            'Passed' => $passed,
            'Failed' => max(0, $taken - $passed),
        ];
    }

    #[Computed]
    public function alumniByRegion(): array
    {
        $q = $this->alumniQuery();
        if (! $q) return [];

        return $q->with('userProfile:id,user_id,location')
            ->get()
            ->map(fn($u) => $u->userProfile?->location['region_name'] ?? null)
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(8)
            ->toArray();
    }

    // ========== ACTIONS ==========

    public function refreshAnalytics()
    {
        // Bust computed caches by unsetting them
        $props = [
            'users',
            'active',
            'alumni',
            'programHeads',
            'alumniByDept',
            'alumniByBatch',
            'courseAnalytics',
            'furtherStudiesRate',
            'employmentStatusBreakdown',
            'employmentTypeBreakdown',
            'organizationTypeBreakdown',
            'employmentAreaBreakdown',
            'monthsToFirstJobBreakdown',
            'genderBreakdown',
            'civilStatusBreakdown',
            'furtherStudiesLevelBreakdown',
            'topEmployers',
            'jobAlignmentBreakdown',
            'boardExamBreakdown',
            'alumniByRegion',
        ];
        foreach ($props as $p) unset($this->{$p});

        $this->dispatch('analytics-refreshed');
    }

    public function updatedSelectedBatchId()
    {
        $this->dispatch('batch-changed');
    }
};
