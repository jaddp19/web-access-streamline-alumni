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
        $this->analyticsInstance = null;
        $this->dispatch('batch-changed');
    }

    protected function analytics(): DashboardAnalytics
    {
        return $this->analyticsInstance ??= new DashboardAnalytics($this->selectedBatchId);
    }

    // ===== Batches =====

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

    /**
     * Nested: department → courses. Used for drill-down.
     */
    #[Computed]
    public function alumniByDeptAndCourse(): array
    {
        return $this->analytics()->alumniByDeptAndCourse();
    }

    #[Computed]
    public function alumniByBatch(): array
    {
        return $this->analytics()->alumniByBatch();
    }

    /**
     * Nested: batch → courses. Used for drill-down.
     */
    #[Computed]
    public function alumniByBatchAndCourse(): array
    {
        return $this->analytics()->alumniByBatchAndCourse();
    }

    #[Computed]
    public function alumniByBatchAndDepartment(): array
    {
        return $this->analytics()->alumniByBatchAndDepartment();
    }

    #[Computed]
    public function courseAnalytics(): array
    {
        return $this->analytics()->courseAnalytics();
    }

    #[Computed]
    public function courseAnalyticsByDept(): array
    {
        return $this->analytics()->courseAnalyticsByDept();
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

    // ===== Sub-breakdowns =====

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

        // ===== Top Notchers =====

    #[Computed]
    public function topNotchersByDeptAndCourse(): array
    {
        return $this->analytics()->topNotchersByDeptAndCourse();
    }

    #[Computed]
    public function topNotcherTotal(): int
    {
        return array_sum(array_column($this->topNotchersByDeptAndCourse, 'total'));
    }

    #[Computed]
    public function topNotcherChampion(): ?array
    {
        $items = $this->topNotchersByDeptAndCourse;

        if (empty($items)) {
            return null;
        }

        // Already sorted DESC by total.
        $key = array_key_first($items);

        return [
            'code'      => $key,
            'name'      => $items[$key]['name'],
            'total'     => $items[$key]['total'],
            'best_rank' => $items[$key]['best_rank'] ?? null,
        ];
    }

    #[Computed]
    public function highestTopNotcher(): ?array
    {
        return $this->analytics()->highestTopNotcher();
    }

    // ===== Manual refresh =====

    public function refreshAnalytics(): void
    {
        $this->analyticsInstance = null;
        $this->dispatch('batch-changed');
    }

        // ===== Board Passers =====

    #[Computed]
    public function boardExamBreakdown(): array
    {
        return $this->analytics()->boardExamBreakdown();
    }

    #[Computed]
    public function boardPassersByDeptAndCourse(): array
    {
        return $this->analytics()->boardPassersByDeptAndCourse();
    }

    #[Computed]
    public function boardPasserTotal(): int
    {
        return array_sum(array_column($this->boardPassersByDeptAndCourse, 'total'));
    }
};