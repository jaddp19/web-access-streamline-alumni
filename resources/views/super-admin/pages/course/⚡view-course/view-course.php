<?php

use App\Models\Course;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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

    public function updatingPage(): void
    {
        $this->clearSelection();
    }

    protected function clearSelection(): void
    {
        $this->selectedPrograms   = [];
        $this->selectAllFiltered  = false;
        $this->selectAllOnPage    = false;
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

    // =========================================================
    //  SELECTION
    // =========================================================

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
    }

    public function toggleRowSelection(int $id): void
    {
        if ($this->selectAllFiltered) {
            // Drop out of "all" mode into specific mode when manually toggling
            $this->selectAllFiltered = false;
            $this->selectedPrograms = $this->pageRowIds;
        }

        if (in_array($id, $this->selectedPrograms, true)) {
            $this->selectedPrograms = array_values(
                array_diff($this->selectedPrograms, [$id])
            );
        } else {
            $this->selectedPrograms[] = $id;
        }

        $this->recomputeSelectAllOnPage();
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
        abort_unless(auth()->user()?->can('manage-courses'), 403);

        if ($this->selectedCount <= 0) {
            session()->flash('error', 'Nothing is selected.');
            return;
        }

        try {
            $count = DB::transaction(function () {
                $query = Course::query();

                if (! $this->selectAllFiltered) {
                    $query->whereIn('id', $this->selectedPrograms);
                }

                return $query->delete();
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Delete failed: ' . $e->getMessage());
            return;
        }

        Cache::forget('courses:count');
        Cache::forget('dashboard:course-analytics');

        $this->clearSelection();
        $this->resetPage();

        session()->flash('success', "{$count} course(s) deleted successfully.");
    }
};