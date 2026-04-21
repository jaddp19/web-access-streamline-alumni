<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app-super-admin')] class extends Component
{
    use WithPagination; // 🔑 enable pagination methods
    public $selectedUsers = []; // @var array $selectedUsers IDs of selected users across all pages
    public $selectAll = false; // @var bool $selectAll Whether all users are selected
    
    /**
     * Delete all selected users.
     * Resets selection after deletion.
     */
    public function deleteSelected()
    {
        User::whereIn('id', $this->selectedUsers)->delete();

        $this->selectedUsers = [];
        $this->selectAll = false;

        session()->flash('success', 'Selected users deleted successfully.');
    }

    public function updatedSelectAll($value) 
    { 
        if ($value) 
        { 
            // Grab IDs from the all pages, not just current page
            $this->selectedUsers = $this->users->getCollection()
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray(); 
        } else { 
            $this->selectedUsers = []; 
        } 
    } 
                     
    public function updatedSelectedUsers() 
    { 
        // Keep header checkbox in sync 
        $this->selectAll = count($this->selectedUsers) === $this->totalUsersCount(); 
    }

    /**
     * Toggle selection of all users across pages.
     */
    public function toggleSelectAll()
    {
        $allIds = User::pluck('id')->map(fn($id) => (int) $id)->toArray();

        $selectedCount = count($this->selectedUsers);
        $totalCount = $this->totalUsersCount;

        if (count($this->selectedUsers) === $this->totalUsersCount()) 
            { 
                $this->selectedUsers = []; 
                $this->selectAll = false; 
            } else { 
                $this->selectedUsers = $allIds; $this->selectAll = true; 
            }
    }

    /**
     * Toggle selection of a single user.
     */
    public function toggleRowSelection($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            // Remove if already selected
            $this->selectedUsers = array_values(array_diff($this->selectedUsers, [$userId]));
        } else {
            // Add if not selected
            $this->selectedUsers[] = $userId;
        }

        // Sync header checkbox
        $this->selectAll = count($this->selectedUsers) === $this->totalUsersCount();
    }
    
    /**
     * Computed property: total number of users.
     */
    #[Computed]
    public function totalUsersCount()
    {
        return User::count();
    }
    
    /**
     * Computed property: paginated users with roles.
     */
    #[Computed()]
    public function users()
    {
        
        return User::with('roles:id,name')
            ->select('id', 'name', 'email', 'created_at')
            ->latest()
            ->paginate(5);
    }
};