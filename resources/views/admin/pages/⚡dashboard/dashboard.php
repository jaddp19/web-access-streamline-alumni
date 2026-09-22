<?php

use App\Models\Department;
use App\Models\User;
use App\Support\DashboardAnalytics;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-admin')] class extends Component
{
    public $selectedBatchId = '';
    protected ?DashboardAnalytics $analyticsInstance = null;

    /** Reset memoized analytics when batch changes. */
    public function updatedSelectedBatchId(): void
    {
        $this->analyticsInstance = null;
        $this->dispatch('batch-changed');
    }

    /**
     * - null  → registrar (global view)
     * - int   → program head (department-scoped)
     * - false → program head without a department (nothing to show)
     */
    #[Computed]
    public function scope(): int|false|null
    {
        $user = Auth::user();
        if (! $user) return false;
        if ($user->hasRole('registrar')) return null;

        return Department::where('program_head_id', $user->id)->value('id') ?? false;
    }

    #[Computed]
    public function isRegistrar(): bool
    {
        return Auth::user()?->hasRole('registrar') ?? false;
    }

    #[Computed]
    public function hasNoDepartment(): bool
    {
        return $this->scope === false;
    }

    #[Computed]
    public function myDepartmentName(): ?string
    {
        return is_int($this->scope)
            ? Department::whereKey($this->scope)->value('dept_name')
            : null;
    }

    /** Memoized analytics instance with batch + dept scope. */
    protected function analytics(): DashboardAnalytics
    {
        $deptId = is_int($this->scope) ? $this->scope : null;

        return $this->analyticsInstance ??= new DashboardAnalytics(
            $this->selectedBatchId ? (int) $this->selectedBatchId : null,
            $deptId,
        );
    }

    // ===== Selector =====

    #[Computed]
    public function batches(): array
    {
        if ($this->hasNoDepartment) return [];
        return $this->analytics()->batches();
    }

    // ===== Stat cards =====

    #[Computed]
    public function users(): int
    {
        if ($this->hasNoDepartment) return 0;
        return $this->analytics()->users();
    }

    #[Computed]
    public function alumni(): int
    {
        if ($this->hasNoDepartment) return 0;
        return $this->analytics()->alumni();
    }

    #[Computed]
    public function boardPassers(): int
    {
        if ($this->hasNoDepartment) return 0;
        return $this->analytics()->boardPassers();
    }

    #[Computed]
    public function pendingVerification(): int
    {
        if ($this->hasNoDepartment) return 0;
        return $this->analytics()->pendingVerifications();
    }

    #[Computed]
    public function courses(): int
    {
        if ($this->hasNoDepartment) return 0;
        return $this->analytics()->courses();
    }

    #[Computed]
    public function programHeads(): int
    {
        if ($this->hasNoDepartment) return 0;
        return $this->analytics()->programHeads();
    }

    // ===== Charts =====

    #[Computed]
    public function alumniByDept(): array
    {
        return $this->hasNoDepartment ? [] : $this->analytics()->alumniByDept();
    }

    #[Computed]
    public function alumniByBatch(): array
    {
        return $this->hasNoDepartment ? [] : $this->analytics()->alumniByBatch();
    }

    #[Computed]
    public function courseAnalytics(): array
    {
        return $this->hasNoDepartment ? [] : $this->analytics()->courseAnalytics();
    }

    #[Computed]
    public function furtherStudiesRate(): int
    {
        return $this->hasNoDepartment ? 0 : $this->analytics()->furtherStudiesRate();
    }

    #[Computed]
    public function tracerBreakdowns(): array
    {
        return $this->hasNoDepartment
            ? ['employment_status' => [], 'employment_type' => [], 'organization_type' => [],
               'employment_area' => [], 'civil_status' => [], 'job_alignment' => [],
               'months_to_first_job' => []]
            : $this->analytics()->tracerBreakdowns();
    }

    // ===== Breakdown props (thin wrappers over tracerBreakdowns) =====

    #[Computed]
    public function employmentStatusBreakdown(): array
    {
        return $this->tracerBreakdowns['employment_status'];
    }

    #[Computed]
    public function employmentTypeBreakdown(): array
    {
        return $this->tracerBreakdowns['employment_type'];
    }

    #[Computed]
    public function organizationTypeBreakdown(): array
    {
        return $this->tracerBreakdowns['organization_type'];
    }

    #[Computed]
    public function employmentAreaBreakdown(): array
    {
        return $this->tracerBreakdowns['employment_area'];
    }

    #[Computed]
    public function civilStatusBreakdown(): array
    {
        return $this->tracerBreakdowns['civil_status'];
    }

    #[Computed]
    public function jobAlignmentBreakdown(): array
    {
        return $this->tracerBreakdowns['job_alignment'];
    }

    #[Computed]
    public function monthsToFirstJobBreakdown(): array
    {
        return $this->tracerBreakdowns['months_to_first_job'];
    }

    #[Computed]
    public function genderBreakdown(): array
    {
        return $this->hasNoDepartment ? [] : $this->analytics()->genderBreakdown();
    }

    #[Computed]
    public function furtherStudiesLevelBreakdown(): array
    {
        return $this->hasNoDepartment ? [] : $this->analytics()->furtherStudiesLevelBreakdown();
    }

    #[Computed]
    public function boardExamBreakdown(): array
    {
        return $this->hasNoDepartment ? [] : $this->analytics()->boardExamBreakdown();
    }

    #[Computed]
    public function topEmployers(): array
    {
        return $this->hasNoDepartment ? [] : $this->analytics()->topEmployers();
    }

    #[Computed]
    public function alumniByRegion(): array
    {
        return $this->hasNoDepartment ? [] : $this->analytics()->alumniByRegion();
    }

    // ===== Manual refresh =====

    public function refreshAnalytics(): void
    {
        $this->analyticsInstance = null;
        $this->dispatch('analytics-refreshed');
    }
};