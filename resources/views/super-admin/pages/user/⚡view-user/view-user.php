<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $roleFilter = 'all';

    public $selectedUsers = [];
    public $selectAll = false;

    public function updatedRoleFilter()
    {
        $this->resetPage();
        $this->selectedUsers = [];
        $this->selectAll = false;
    }

    public function setRoleFilter(string $role)
    {
        $this->roleFilter = $role;
        $this->updatedRoleFilter();
    }

    /**
     * Delete all selected users.
     * Resets selection after deletion.
     */
    public function deleteSelected()
    {
        User::role(['alumni', 'super-admin', 'admin'])->whereIn('id', $this->selectedUsers)->delete();

        $this->selectedUsers = [];
        $this->selectAll = false;

        session()->flash('success', 'Selected users deleted successfully.');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Grab IDs from all pages under the current filter, not just current page
            $this->selectedUsers = $this->filteredQuery()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function updatedSelectedUsers()
    {
        // Keep header checkbox in sync
        $this->selectAll = count($this->selectedUsers) === $this->totalUsersCount;
    }

    /**
     * Toggle selection of all users across pages (respecting the active filter).
     */
    public function toggleSelectAll()
    {
        if (count($this->selectedUsers) === $this->totalUsersCount) {
            $this->selectedUsers = [];
            $this->selectAll = false;
        } else {
            $this->selectedUsers = $this->filteredQuery()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
            $this->selectAll = true;
        }
    }

    /**
     * Toggle selection of a single user.
     */
    public function toggleRowSelection($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_values(array_diff($this->selectedUsers, [$userId]));
        } else {
            $this->selectedUsers[] = $userId;
        }

        $this->selectAll = count($this->selectedUsers) === $this->totalUsersCount;
    }

    /**
     * Shared base query respecting the active role filter tab.
     */
    protected function filteredQuery()
    {
        return User::role(['alumni', 'super-admin', 'admin'])
            ->when($this->roleFilter !== 'all', function ($query) {
                $query->role($this->roleFilter);
            });
    }

    /**
     * Computed property: total number of users under the active filter.
     */
    #[Computed]
    public function totalUsersCount()
    {
        return $this->filteredQuery()->count();
    }

    /**
     * Computed property: paginated users with roles, under the active filter.
     */
    #[Computed]
    public function users()
    {
        return $this->filteredQuery()
            ->with('roles:id,name')
            ->select('id', 'name', 'email', 'created_at')
            ->latest()
            ->paginate(5);
    }
};
