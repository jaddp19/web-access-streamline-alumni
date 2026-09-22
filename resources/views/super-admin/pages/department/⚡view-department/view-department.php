<?php

use App\Models\Department;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public array $selectedDepartments = [];
    public bool $selectAllFiltered = false;
    public bool $selectAllOnPage = false;

    protected int $perPage = 10;

    public function updatingPage(): void
    {
        $this->clearSelection();
    }

    protected function clearSelection(): void
    {
        $this->selectedDepartments = [];
        $this->selectAllFiltered  = false;
        $this->selectAllOnPage    = false;
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function departments()
    {
        return Department::query()
            ->select('id', 'dept_name', 'dept_desc', 'dept_code', 'dept_logo', 'created_at', 'is_active')
            ->latest()
            ->paginate($this->perPage);
    }

    #[Computed]
    public function totalDepartmentsCount(): int
    {
        return Cache::remember('dept:count', now()->addSeconds(30), function () {
            return Department::count();
        });
    }

    #[Computed]
    public function pageRowIds(): array
    {
        return $this->departments->getCollection()->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    #[Computed]
    public function selectedCount(): int
    {
        return $this->selectAllFiltered
            ? $this->totalDepartmentsCount
            : count($this->selectedDepartments);
    }

    // =========================================================
    //  SELECTION
    // =========================================================

    public function updatedSelectAllDepartments($value): void
    {
        if ($value) {
            $this->selectAllFiltered = true;
            $this->selectedDepartments = [];
            $this->selectAllOnPage = true;
        } else {
            $this->clearSelection();
        }
    }

    public function toggleSelectAllOnPage(): void
    {
        $pageIds = $this->pageRowIds;

        if (empty($pageIds)) {
            return;
        }

        $allOnPageSelected = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedDepartments));

        if ($allOnPageSelected && ! $this->selectAllFiltered) {
            $this->selectedDepartments = array_values(
                array_diff($this->selectedDepartments, $pageIds)
            );
        } else {
            $this->selectedDepartments = array_values(array_unique(
                array_merge($this->selectedDepartments, $pageIds)
            ));
        }

        $this->recomputeSelectAllOnPage();
    }

    public function toggleRowSelection(int $id): void
    {
        if ($this->selectAllFiltered) {
            // Disallow checkbox toggles while in "select all" mode.
            $this->selectAllFiltered = false;
            $this->selectedDepartments = $this->pageRowIds;
        }

        if (in_array($id, $this->selectedDepartments, true)) {
            $this->selectedDepartments = array_values(
                array_diff($this->selectedDepartments, [$id])
            );
        } else {
            $this->selectedDepartments[] = $id;
        }

        $this->recomputeSelectAllOnPage();
    }

    public function isRowSelected(int $id): bool
    {
        if ($this->selectAllFiltered) {
            return true;
        }
        return in_array($id, $this->selectedDepartments, true);
    }

    protected function recomputeSelectAllOnPage(): void
    {
        $pageIds = $this->pageRowIds;

        $this->selectAllOnPage = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedDepartments));
    }

    // =========================================================
    //  ACTIONS
    // =========================================================

    public function deleteSelected(): void
    {
        abort_unless(auth()->user()?->can('manage-departments'), 403);

        if ($this->selectedCount <= 0) {
            session()->flash('error', 'Nothing is selected.');
            return;
        }

        try {
            $result = DB::transaction(function () {
                $query = Department::query();

                if (! $this->selectAllFiltered) {
                    $query->whereIn('id', $this->selectedDepartments);
                }

                // Collect logos first for cleanup.
                $logos = (clone $query)->pluck('dept_logo', 'id')->all();

                $count = $query->delete();

                return ['count' => $count, 'logos' => $logos];
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Delete failed: ' . $e->getMessage());
            return;
        }

        // Delete logo files after DB commit.
        foreach ($result['logos'] as $deptId => $logo) {
            $this->deleteLogoFile($logo, (int) $deptId);
        }

        Cache::forget('dept:count');
        Cache::forget('assign:active-departments:v2');
        Cache::forget('dept:ph_count');

        $this->clearSelection();
        $this->resetPage();

        session()->flash('success', "{$result['count']} department(s) deleted successfully.");
    }

    public function deleteDepartment(int $id): void
    {
        abort_unless(auth()->user()?->can('manage-departments'), 403);

        $department = Department::find($id);
        if (! $department) {
            return;
        }

        $name = $department->dept_name;
        $logo = $department->dept_logo;

        try {
            DB::transaction(fn () => $department->delete());
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Delete failed. Please try again.');
            return;
        }

        $this->deleteLogoFile($logo, $id);

        Cache::forget('dept:count');
        Cache::forget('assign:active-departments:v2');
        Cache::forget('dept:ph_count');

        $this->selectedDepartments = array_values(
            array_diff($this->selectedDepartments, [$id])
        );
        $this->recomputeSelectAllOnPage();

        session()->flash('success', "Department \"{$name}\" deleted.");
    }

    /**
     * Safely delete a logo file from the public storage disk.
     */
    protected function deleteLogoFile(?string $path, ?int $exceptId = null): void
    {
        if (blank($path)) return;

        if (filter_var($path, FILTER_VALIDATE_URL)) return;
        if (str_starts_with($path, '/imgs/')) return;

        $relative = ltrim($path, '/');

        // Still used by another row? Leave it alone.
        $stillUsed = Department::query()
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->where('dept_logo', $path)
            ->exists();

        if ($stillUsed) return;

        // Single-filesystem-hit delete (delete() returns bool without exists check).
        Storage::disk('public')->delete($relative);
    }
};