<?php

use App\Models\Department;
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
     * Delete all selected Departments.
     * Resets selection after deletion.
     */
    public function deleteSelected()
    {
        Department::whereIn('id', $this->selectedDepartments)->delete();

        $this->selectedDepartments = [];
        $this->selectAll = false;

        session()->flash('success', 'Selected departments deleted successfully.');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Grab IDs from all pages, not just current page
            $this->selectedDepartments = Department::pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        } else {
            $this->selectedDepartments = [];
        }
    }

    public function updatedSelectedDepartments()
    {
        // Keep header checkbox in sync
        $this->selectAll = count($this->selectedDepartments) === $this->totalDepartmentsCount;
    }

    /**
     * Toggle selection of all Departments across pages.
     */
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

    /**
     * Toggle selection of a single department.
     */
    public function toggleRowSelection($departmentId)
    {
        if (in_array($departmentId, $this->selectedDepartments)) {
            $this->selectedDepartments = array_values(array_diff($this->selectedDepartments, [$departmentId]));
        } else {
            $this->selectedDepartments[] = $departmentId;
        }

        $this->selectAll = count($this->selectedDepartments) === $this->totalDepartmentsCount;
    }

    /**
     * Computed property: total number of departments.
     */
    #[Computed]
    public function totalDepartmentsCount()
    {
        return Department::count();
    }

    /**
     * Computed property: paginated departments.
     */
    #[Computed]
    public function departments()
    {
        return Department::select('id', 'dept_name', 'dept_desc' ,'dept_code','dept_logo','created_at')->latest()->paginate(5);
    }
};
