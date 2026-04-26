<?php

use App\Models\DegreeProgram;
use App\Models\Department;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app-super-admin')] class extends Component
{
    use WithPagination; // 🔑 enable pagination methods
    public $selectedPrograms = []; // @var array $selectedPrograms IDs of selected programs across all pages
    public $selectAll = false; // @var bool $selectAll Whether all programs are selected
    
    /**
     * Delete all selected programs.
     * Resets selection after deletion.
     */
    public function deleteSelected()
    {
        DegreeProgram::whereIn('id', $this->selectedPrograms)->delete();

        $this->selectedPrograms = [];
        $this->selectAll = false;

        session()->flash('success', 'Selected programs deleted successfully.');
    }

    public function updatedSelectAll($value) 
    { 
        if ($value) 
        { 
            // Grab IDs from the all pages, not just current page
            $this->selectedPrograms = $this->programs->getCollection()
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray(); 
        } else { 
            $this->selectedPrograms = []; 
        } 
    } 
                     
    public function updatedSelectedPrograms() 
    { 
        // Keep header checkbox in sync 
        $this->selectAll = count($this->selectedPrograms) === $this->totalProgramsCount(); 
    }

    /**
     * Toggle selection of all programs across pages.
     */
    public function toggleSelectAll()
    {
        $allIds = DegreeProgram::pluck('id')->map(fn($id) => (int) $id)->toArray();

        $selectedCount = count($this->selectedPrograms);
        $totalCount = $this->totalProgramsCount;

        if (count($this->selectedPrograms) === $this->totalProgramsCount()) 
            { 
                $this->selectedPrograms = []; 
                $this->selectAll = false; 
            } else { 
                $this->selectedPrograms = $allIds; $this->selectAll = true; 
            }
    }

    /**
     * Toggle selection of a single user.
     */
    public function toggleRowSelection($programId)
    {
        if (in_array($programId, $this->selectedPrograms)) {
            // Remove if already selected
            $this->selectedPrograms = array_values(array_diff($this->selectedPrograms, [$programId]));
        } else {
            // Add if not selected
            $this->selectedPrograms[] = $programId;
        }

        // Sync header checkbox
        $this->selectAll = count($this->selectedPrograms) === $this->totalProgramsCount();
    }

    
    /**
     * Computed property: total number of programs.
     */
    #[Computed]
    public function totalProgramsCount()
    {
        return DegreeProgram::count();
    }
    
    /**
     * Computed property: paginated programs.
     */
    #[Computed()]
    public function programs()
    {
        
        return DegreeProgram::with('department:id,department_name')->select('id', 'program_name', 'department_id', 'created_at')->latest()->paginate(10);
    }
};