<?php

use App\Models\Department;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app-super-admin')] class extends Component
{
    use WithPagination; // 🔑 enable pagination methods
    public $selectedDepartments = []; // @var array $selectedDepartments IDs of selected Departments across all pages
    public $selectAll = false; // @var bool $selectAll Whether all Departments are selected
    
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
        if ($value) 
        { 
            // Grab IDs from the all pages, not just current page
            $this->selectedDepartments = $this->departments->getCollection()
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray(); 
        } else { 
            $this->selectedDepartments = []; 
        } 
    } 
                     
    public function updatedSelectedDepartments() 
    { 
        // Keep header checkbox in sync 
        $this->selectAll = count($this->selectedDepartments) === $this->totalDepartmentsCount(); 
    }

    /**
     * Toggle selection of all Departments across pages.
     */
    public function toggleSelectAll()
    {
        $allIds = Department::pluck('id')->map(fn($id) => (int) $id)->toArray();

        $selectedCount = count($this->selectedDepartments);
        $totalCount = $this->totalDepartmentsCount;

        if (count($this->selectedDepartments) === $this->totalDepartmentsCount()) 
            { 
                $this->selectedDepartments = []; 
                $this->selectAll = false; 
            } else { 
                $this->selectedDepartments = $allIds; $this->selectAll = true; 
            }
    }

    /**
     * Toggle selection of a single user.
     */
    public function toggleRowSelection($departmentId)
    {
        if (in_array($departmentId, $this->selectedDepartments)) {
            // Remove if already selected
            $this->selectedDepartments = array_values(array_diff($this->selectedDepartments, [$departmentId]));
        } else {
            // Add if not selected
            $this->selectedDepartments[] = $departmentId;
        }

        // Sync header checkbox
        $this->selectAll = count($this->selectedDepartments) === $this->totalDepartmentsCount();
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
    #[Computed()]
    public function departments()
    {
        
        return Department::select('id', 'department_name', 'created_at')->latest()->paginate(5);
    }
};