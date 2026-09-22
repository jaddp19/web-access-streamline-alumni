<?php

use App\Support\DashboardAnalytics;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public ?int $selectedBatchId = null;

    /** Memoized analytics instance for this request. */
    protected ?DashboardAnalytics $analyticsInstance = null;

    public function updatedSelectedBatchId(): void
    {
        // Reset memoized instance so a fresh batch context is used.
        $this->analyticsInstance = null;
        $this->dispatch('batch-changed');
    }

    protected function analytics(): DashboardAnalytics
    {
        return $this->analyticsInstance ??= new DashboardAnalytics($this->selectedBatchId);
    }

    // ===== Batches (doesn't depend on batch → persist) =====

    #[Computed(persist: true)]
    public function batches(): array
    {
        return $this->analytics()->batches();
    }

    // ===== Stat cards =====

    #[Computed(persist: true)]
    public function users(): int
    {
        return $this->analytics()->users();
    }

    #[Computed]
    public function alumni(): int
    {
        return $this->analytics()->alumni();
    }

    #[Computed(persist: true)]
    public function programHeads(): int
    {
        return $this->analytics()->programHeads();
    }

    #[Computed(persist: true)]
    public function courses(): int
    {
        return $this->analytics()->courses();
    }

    // ===== Charts =====

    #[Computed]
    public function alumniByDept(): array
    {
        return $this->analytics()->alumniByDept();
    }

    #[Computed]
    public function alumniByBatch(): array
    {
        return $this->analytics()->alumniByBatch();
    }

    #[Computed]
    public function courseAnalytics(): array
    {
        return $this->analytics()->courseAnalytics();
    }

    #[Computed]
    public function tracerBreakdowns(): array
    {
        return $this->analytics()->tracerBreakdowns();
    }

    #[Computed]
    public function furtherStudiesRate(): int
    {
        return $this->analytics()->furtherStudiesRate();
    }

    // ===== Sub-breakdowns (reuse cached tracerBreakdowns) =====

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
    public function monthsToFirstJobBreakdown(): array
    {
        return $this->tracerBreakdowns['months_to_first_job'];
    }

    // ===== Manual refresh =====

    public function refreshAnalytics(): void
    {
        $this->analyticsInstance = null;
        $this->dispatch('batch-changed');
    }
};