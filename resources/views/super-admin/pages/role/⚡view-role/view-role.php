<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination; // 🔑 enable pagination methods
    public $selectedRoles = []; 
    public $selectAll = false;
    public function deleteSelected()
    {
        Role::whereIn('id', $this->selectedRoles)->delete();

        $this->selectedRoles = [];
        $this->selectAll = false;

        session()->flash('success', 'Selected roles deleted successfully.');
    }

    public function updatedSelectAll($value) 
    { 
        if ($value) 
        { 
            // Grab IDs from the current page 
            $this->selectedRoles = $this->roles->getCollection()
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray(); 
        } else { 
            $this->selectedRoles = []; 
        } 
    } 
                     
    public function updatedselectedRoles() 
    { 
        // Keep header checkbox in sync 
        $this->selectAll = count($this->selectedRoles) === $this->totalRolesCount(); 
    }

    public function toggleSelectAll()
    {
        $allIds = Role::pluck('id')->map(fn($id) => (int) $id)->toArray();

        $selectedCount = count($this->selectedRoles);
        $totalCount = $this->totalRolesCount;

        if ($selectedCount === $totalCount) {
            $this->selectedRoles = [];
            $this->selectAll = false;
        } else {
            $this->selectedRoles = $allIds;
            $this->selectAll = true;
        }
    }

    public function toggleRowSelection($userId)
    {
        if (in_array($userId, $this->selectedRoles)) {
            // Remove if already selected
            $this->selectedRoles = array_values(array_diff($this->selectedRoles, [$userId]));
        } else {
            // Add if not selected
            $this->selectedRoles[] = $userId;
        }

        // Sync header checkbox
        $this->selectAll = count($this->selectedRoles) === $this->totalRolesCount();
    }
    #[Computed]
    public function totalRolesCount()
    {
        return Role::count();
    }
    
    #[Computed()]
    public function roles()
    {
        return Role::with('permissions')
            ->select('id','name','created_at')
            ->latest()
            ->paginate(5);
    }
    
};