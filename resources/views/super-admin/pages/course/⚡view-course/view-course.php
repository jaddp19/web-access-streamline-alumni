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

    // --- ADDED FOR VIEW MODAL ---
    public ?int $viewingProgramId = null;

    public function openView(int $id): void
    {
        $this->viewingProgramId = $id;
    }

    public function closeView(): void
    {
        $this->viewingProgramId = null;
    }

    #[Computed]
    public function viewingProgram(): ?Course
    {
        if (! $this->viewingProgramId) return null;

        return Course::with('department:id,dept_name')
            ->withCount(['userProfiles' => fn ($q) => $q
                ->where('is_approved', true)
                ->whereHas('user', fn ($u) => $u->role('alumni')),
            ])
            ->find($this->viewingProgramId);
    }
    // ----------------------------

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
        $this->selectedPrograms = [];
        $this->selectAllFiltered = false;
        $this->selectAllOnPage = false;

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
            ->select('id', 'course_code', 'course_title', 'course_desc', 'course_type', 'is_active', 'department_id', 'created_at')
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

    public function toggleSelectAll(): void
    {
        if ($this->allSelected) {
            $this->clearSelection();
            return;
        }

        $this->selectAllFiltered = true;
        $this->selectedPrograms = [];
        $this->selectAllOnPage = true;

        unset($this->selectedCount);
        unset($this->allSelected);
    }

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
        Gate::authorize('can_delete');
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

                return Course::deleteWithAudit(
                    Course::whereIn('id', $targetIds)
                );
            });
        } catch (Throwable $e) {
            report($e);
            session()->flash('error', 'Delete failed. Please try again.');
            return;
        }

        Cache::forget('courses:count');
        Cache::forget('dashboard:course-analytics');

        $this->selectedPrograms = [];
        $this->selectAllFiltered = false;
        $this->selectAllOnPage = false;

        unset($this->selectedCount);
        unset($this->totalProgramsCount);
        unset($this->pageRowIds);
        unset($this->programs);
        unset($this->allSelected);

        $this->resetPage();

        session()->flash('success', "{$count} course(s) deleted successfully.");
    }
};