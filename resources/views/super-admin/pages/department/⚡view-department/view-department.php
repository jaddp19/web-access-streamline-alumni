<?php

use App\Models\Department;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public $selectedDepartments = [];
    public $selectAll = false;

    /**
     * Delete all selected Departments + their logo files from storage.
     */
    public function deleteSelected()
    {
        $departments = Department::whereIn('id', $this->selectedDepartments)->get();

        foreach ($departments as $department) {
            $this->deleteLogoFile($department->dept_logo, $department->id);
        }

        Department::whereIn('id', $this->selectedDepartments)->delete();

        $this->selectedDepartments = [];
        $this->selectAll = false;

        session()->flash('success', 'Selected departments and their logos deleted successfully.');
    }

    /**
     * Delete a single department + its logo file.
     * (Use this from the row-level delete button if you have one.)
     */
    public function deleteDepartment(int $id): void
    {
        $department = Department::find($id);
        if (! $department) return;

        $this->deleteLogoFile($department->dept_logo, $department->id);
        $department->delete();

        // Clean up selection state
        $this->selectedDepartments = array_values(
            array_diff($this->selectedDepartments, [$id])
        );
        $this->selectAll = count($this->selectedDepartments) === $this->totalDepartmentsCount;

        session()->flash('success', "Department \"{$department->dept_name}\" deleted.");
    }

    /**
     * Safely delete a logo file from the public storage disk.
     *
     * Skips:
     *  - null / empty values
     *  - full external URLs (http/https)
     *  - files shared by other departments (i.e. the default CSAV logo)
     *
     * @param string|null $path   Value stored in departments.dept_logo
     * @param int|null    $exceptId  Exclude this department when checking for other references
     */
    protected function deleteLogoFile(?string $path, ?int $exceptId = null): void
    {
        if (blank($path)) return;

        // External URLs — never touch
        if (filter_var($path, FILTER_VALIDATE_URL)) return;

        // Normalise: strip leading slash if present
        $relative = ltrim($path, '/');

        // If it's still a public asset path (e.g. /imgs/...), skip — that's not in storage
        if (str_starts_with($path, '/imgs/')) return;

        // Check whether any OTHER department still references this exact path
        $stillUsed = Department::query()
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->where('dept_logo', $path)
            ->exists();

        if ($stillUsed) {
            // Shared logo (e.g. the default one) — leave it alone
            return;
        }

        // Delete from storage if it exists
        if (Storage::disk('public')->exists($relative)) {
            Storage::disk('public')->delete($relative);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedDepartments = Department::pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        } else {
            $this->selectedDepartments = [];
        }
    }

    public function updatedSelectedDepartments()
    {
        $this->selectAll = count($this->selectedDepartments) === $this->totalDepartmentsCount;
    }

    public function toggleSelectAll()
    {
        if (count($this->selectedDepartments) === $this->totalDepartmentsCount) {
            $this->selectedDepartments = [];
            $this->selectAll = false;
        } else {
            $this->selectedDepartments = Department::pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
            $this->selectAll = true;
        }
    }

    public function toggleRowSelection($departmentId)
    {
        if (in_array($departmentId, $this->selectedDepartments)) {
            $this->selectedDepartments = array_values(
                array_diff($this->selectedDepartments, [$departmentId])
            );
        } else {
            $this->selectedDepartments[] = $departmentId;
        }

        $this->selectAll = count($this->selectedDepartments) === $this->totalDepartmentsCount;
    }

    #[Computed]
    public function totalDepartmentsCount()
    {
        return Department::count();
    }

    #[Computed]
    public function departments()
    {
        return Department::select('id', 'dept_name', 'dept_desc', 'dept_code', 'dept_logo', 'created_at', 'is_active')
            ->latest()
            ->paginate(5);
    }
};