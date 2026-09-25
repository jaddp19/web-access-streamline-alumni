<?php

use App\Models\Department;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    /** User chose "select everything across pages". */
    public bool $selectAllFiltered = false;

    /** Specific keys when NOT in select-all-filtered mode. */
    public array $selectedProgramHeads = [];

    /** Keys to EXCLUDE when in select-all-filtered mode (Gmail-style). */
    public array $excludedProgramHeads = [];

    /** Current page all-selected (UI state for the header checkbox). */
    public bool $selectAllOnPage = false;

    protected int $perPage = 10;

    /**
     * Called AFTER the page changes. Selection intentionally persists —
     * we only refresh memoized computeds and the page indicator.
     */
    public function updatedPage(): void
    {
        unset($this->pageRowKeys);
        unset($this->selectedCount);
        unset($this->allSelected);
        unset($this->programHeads);

        $this->recomputeSelectAllOnPage();
    }

    protected function clearSelection(): void
    {
        $this->selectedProgramHeads = [];
        $this->excludedProgramHeads = [];
        $this->selectAllFiltered = false;
        $this->selectAllOnPage = false;

        unset($this->selectedCount);
        unset($this->allSelected);
        unset($this->pageRowKeys);
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    /**
     * simplePaginate = no COUNT query. We already have our own cached
     * count — no reason to hit the DB twice per render.
     */
    #[Computed]
    public function programHeads()
    {
        return Department::query()
            ->whereNotNull('program_head_id')
            ->with(['programHead:id,name,email'])
            ->select('id', 'dept_name', 'program_head_id', 'updated_at')
            ->orderByDesc('updated_at')
            ->simplePaginate($this->perPage);
    }

    /**
     * Cached for 30 seconds — COUNT on a big table without an index
     * is expensive and this number doesn't change every second.
     */
    #[Computed]
    public function totalProgramHeadsCount(): int
    {
        return Cache::remember('dept:ph_count', now()->addSeconds(30), function () {
            return Department::query()->whereNotNull('program_head_id')->count();
        });
    }

    /** Keys for the current page's rows. */
    #[Computed]
    public function pageRowKeys(): array
    {
        return $this->programHeads
            ->getCollection()
            ->map(fn ($d) => $d->id.'-'.$d->program_head_id)
            ->all();
    }

    /** How many rows are actually selected (handles exclusions). */
    #[Computed]
    public function selectedCount(): int
    {
        if ($this->selectAllFiltered) {
            return max(0, $this->totalProgramHeadsCount - count($this->excludedProgramHeads));
        }

        return count($this->selectedProgramHeads);
    }

    /**
     * True when EVERY assignment across every page is selected.
     * Drives the header checkbox state.
     */
    #[Computed]
    public function allSelected(): bool
    {
        if ($this->selectAllFiltered) {
            // All selected unless the user has unchecked at least one row.
            return count($this->excludedProgramHeads) === 0;
        }

        $total = $this->totalProgramHeadsCount;

        return $total > 0 && count($this->selectedProgramHeads) >= $total;
    }

    /** For the footer: total pages, first/last item numbers. */
    #[Computed]
    public function lastPage(): int
    {
        return max(1, (int) ceil($this->totalProgramHeadsCount / $this->perPage));
    }

    #[Computed]
    public function firstItem(): int
    {
        $total = $this->totalProgramHeadsCount;

        return $total === 0 ? 0 : (($this->programHeads->currentPage() - 1) * $this->perPage) + 1;
    }

    #[Computed]
    public function lastItem(): int
    {
        return min(
            $this->programHeads->currentPage() * $this->perPage,
            $this->totalProgramHeadsCount
        );
    }

    // =========================================================
    //  SELECTION
    // =========================================================

    /**
     * Header checkbox — toggles ALL assignments across ALL pages.
     */
    public function toggleSelectAll(): void
    {
        if ($this->allSelected) {
            $this->clearSelection();
            return;
        }

        $this->selectAllFiltered = true;
        $this->excludedProgramHeads = [];
        $this->selectedProgramHeads = [];
        $this->selectAllOnPage = true;

        unset($this->selectedCount);
        unset($this->allSelected);
    }

    /** Bulk-bar button alias — same effect as the header checkbox. */
    public function selectAllMatching(): void
    {
        $this->selectAllFiltered = true;
        $this->excludedProgramHeads = [];
        $this->selectedProgramHeads = [];
        $this->selectAllOnPage = true;

        unset($this->selectedCount);
        unset($this->allSelected);
    }

    /** Mobile footer — toggles just the current page. */
    public function toggleSelectAllOnPage(): void
    {
        $pageKeys = $this->pageRowKeys;

        if (empty($pageKeys)) {
            return;
        }

        $allOnPageSelected = collect($pageKeys)
            ->every(fn ($k) => $this->isRowSelected($k));

        if ($allOnPageSelected) {
            // Deselect this page only
            if ($this->selectAllFiltered) {
                $this->excludedProgramHeads = array_values(array_unique(
                    array_merge($this->excludedProgramHeads, $pageKeys)
                ));
            } else {
                $this->selectedProgramHeads = array_values(
                    array_diff($this->selectedProgramHeads, $pageKeys)
                );
            }
        } else {
            // Select this page only
            if ($this->selectAllFiltered) {
                $this->excludedProgramHeads = array_values(
                    array_diff($this->excludedProgramHeads, $pageKeys)
                );
            } else {
                $this->selectedProgramHeads = array_values(array_unique(
                    array_merge($this->selectedProgramHeads, $pageKeys)
                ));
            }
        }

        $this->recomputeSelectAllOnPage();

        unset($this->selectedCount);
        unset($this->allSelected);
    }

    public function toggleRowSelection(string $key): void
    {
        if ($this->selectAllFiltered) {
            // In "all" mode, toggling = adding/removing an exception.
            if (in_array($key, $this->excludedProgramHeads, true)) {
                $this->excludedProgramHeads = array_values(
                    array_diff($this->excludedProgramHeads, [$key])
                );
            } else {
                $this->excludedProgramHeads[] = $key;
            }
        } else {
            if (in_array($key, $this->selectedProgramHeads, true)) {
                $this->selectedProgramHeads = array_values(
                    array_diff($this->selectedProgramHeads, [$key])
                );
            } else {
                $this->selectedProgramHeads[] = $key;
            }
        }

        $this->recomputeSelectAllOnPage();

        unset($this->selectedCount);
        unset($this->allSelected);
    }

    public function isRowSelected(string $key): bool
    {
        if ($this->selectAllFiltered) {
            return ! in_array($key, $this->excludedProgramHeads, true);
        }

        return in_array($key, $this->selectedProgramHeads, true);
    }

    protected function recomputeSelectAllOnPage(): void
    {
        $pageKeys = $this->pageRowKeys;

        $this->selectAllOnPage = ! empty($pageKeys)
            && collect($pageKeys)->every(fn ($k) => $this->isRowSelected($k));
    }

    // =========================================================
    //  ACTIONS
    // =========================================================

    public function deleteSelected(): void
    {
        $selectedCount = $this->selectAllFiltered
            ? max(0, $this->totalProgramHeadsCount - count($this->excludedProgramHeads))
            : count($this->selectedProgramHeads);

        if ($selectedCount <= 0) {
            session()->flash('error', 'Nothing is selected.');

            return;
        }

        try {
            $count = DB::transaction(function () {
                // ---- Select-all (minus exclusions) ----
                if ($this->selectAllFiltered) {
                    $excludedDeptIds = collect($this->excludedProgramHeads)
                        ->map(fn ($k) => (int) explode('-', $k, 2)[0])
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    return Department::query()
                        ->whereNotNull('program_head_id')
                        ->when(! empty($excludedDeptIds), fn ($q) => $q->whereNotIn('id', $excludedDeptIds))
                        ->update(['program_head_id' => null]);
                }

                // ---- Specific rows (deptId from each "deptId-userId" key) ----
                $deptIds = collect($this->selectedProgramHeads)
                    ->map(fn ($k) => (int) explode('-', $k, 2)[0])
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                if (empty($deptIds)) {
                    return 0;
                }

                return Department::query()
                    ->whereIn('id', $deptIds)
                    ->whereNotNull('program_head_id')
                    ->update(['program_head_id' => null]);
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Delete failed: '.$e->getMessage());

            return;
        }

        Cache::forget('dept:ph_count');
        Cache::forget('dept:count');
        Cache::forget('assign:active-departments:v2');

        $this->clearSelection();
        $this->resetPage();

        if ($count === 0) {
            session()->flash('warning', 'Nothing matched — the assignments may already have been removed.');

            return;
        }

        session()->flash('success', "{$count} program head assignment(s) removed successfully.");
    }
};