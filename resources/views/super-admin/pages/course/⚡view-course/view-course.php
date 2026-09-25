<?php

use App\Models\Course;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public array $selectedPrograms = [];
    public bool $selectAllFiltered = false;
    public bool $selectAllOnPage = false;

    protected int $perPage = 10;

    public function updatedPage(): void
    {
        unset($this->pageRowIds);
        unset($this->selectedCount);
        unset($this->allSelected);

        $this->recomputeSelectAllOnPage();
    }

    protected function clearSelection(): void
    {
        $this->selectedPrograms   = [];
        $this->selectAllFiltered  = false;
        $this->selectAllOnPage    = false;

        unset($this->selectedCount);
        unset($this->pageRowIds);
        unset($this->allSelected);
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function programs()
    {
        return Course::query()
            ->with('department:id,dept_name')
            ->select('id', 'course_code', 'course_title', 'department_id', 'created_at')
            ->latest()
            ->paginate($this->perPage);
    }

    #[Computed]
    public function totalProgramsCount(): int
    {
        return Cache::remember('courses:count', now()->addSeconds(30), function () {
            return Course::count();
        });
    }

    #[Computed]
    public function pageRowIds(): array
    {
        return $this->programs->getCollection()->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    #[Computed]
    public function selectedCount(): int
    {
        return $this->selectAllFiltered
            ? $this->totalProgramsCount
            : count($this->selectedPrograms);
    }

    /**
     * True when EVERY course across every page is selected.
     * Used for the header checkbox state.
     */
    #[Computed]
    public function allSelected(): bool
    {
        if ($this->selectAllFiltered) {
            return true;
        }

        $total = $this->totalProgramsCount;

        return $total > 0 && count($this->selectedPrograms) === $total;
    }

    // =========================================================
    //  SELECTION
    // =========================================================

    /**
     * Header checkbox — toggles ALL courses across ALL pages.
     */
    public function toggleSelectAll(): void
    {
        if ($this->allSelected) {
            $this->clearSelection();
            return;
        }

        // Flip into "all selected" mode. deleteSelected() and
        // isRowSelected() both honor this flag, so every row everywhere
        // is treated as selected without needing to load all IDs.
        $this->selectAllFiltered = true;
        $this->selectedPrograms  = [];
        $this->selectAllOnPage   = true;

        unset($this->selectedCount);
        unset($this->allSelected);
    }

    /**
     * Footer button on mobile — toggles just the current page.
     */
    public function toggleSelectAllOnPage(): void
    {
        $pageIds = $this->pageRowIds;

        if (empty($pageIds)) {
            return;
        }

        $allOnPageSelected = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedPrograms));

        if ($allOnPageSelected && ! $this->selectAllFiltered) {
            $this->selectedPrograms = array_values(
                array_diff($this->selectedPrograms, $pageIds)
            );
        } else {
            $this->selectedPrograms = array_values(array_unique(
                array_merge($this->selectedPrograms, $pageIds)
            ));
        }

        $this->recomputeSelectAllOnPage();

        unset($this->selectedCount);
        unset($this->allSelected);
    }

    public function toggleRowSelection(int $id): void
    {
        // If we're in "all selected" mode, materialize the full set of
        // course IDs first, then fall through to the normal toggle. This
        // makes unchecking a single row leave everything else selected.
        if ($this->selectAllFiltered) {
            $this->selectAllFiltered = false;
            $this->selectedPrograms = Course::query()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if (in_array($id, $this->selectedPrograms, true)) {
            $this->selectedPrograms = array_values(
                array_diff($this->selectedPrograms, [$id])
            );
        } else {
            $this->selectedPrograms[] = $id;
        }

        $this->recomputeSelectAllOnPage();

        unset($this->selectedCount);
        unset($this->allSelected);
    }

    public function isRowSelected(int $id): bool
    {
        if ($this->selectAllFiltered) {
            return true;
        }
        return in_array($id, $this->selectedPrograms, true);
    }

    protected function recomputeSelectAllOnPage(): void
    {
        $pageIds = $this->pageRowIds;

        $this->selectAllOnPage = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedPrograms));
    }

    

    // =========================================================
    //  ACTIONS
    // =========================================================

    public function deleteSelected(): void
    {
        if ($this->selectedCount <= 0) {
            session()->flash('error', 'Nothing is selected.');
            return;
        }

        try {
            $count = DB::transaction(function () {
                $targetIds = Course::query()
                    ->when(! $this->selectAllFiltered, fn ($q) => $q->whereIn('id', $this->selectedPrograms))
                    ->pluck('id')
                    ->all();

                if (empty($targetIds)) {
                    return 0;
                }

                if (Schema::hasTable('student_course')) {
                    DB::table('student_course')
                        ->whereIn('course_id', $targetIds)
                        ->delete();
                }

                return Course::whereIn('id', $targetIds)->delete();
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Delete failed: ' . $e->getMessage());
            return;
        }

        Cache::forget('courses:count');
        Cache::forget('dashboard:course-analytics');

        $this->selectedPrograms  = [];
        $this->selectAllFiltered = false;
        $this->selectAllOnPage   = false;

        unset($this->selectedCount);
        unset($this->totalProgramsCount);
        unset($this->pageRowIds);
        unset($this->programs);
        unset($this->allSelected);

        $this->resetPage();

        session()->flash('success', "{$count} course(s) deleted successfully.");
    }
};