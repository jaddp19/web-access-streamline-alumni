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
    public string $search = ''; // <-- add search property

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

    public function deleteSelected()
    {
        User::role(['alumni', 'registrar', 'program head'])->whereIn('id', $this->selectedUsers)->delete();

        $this->selectedUsers = [];
        $this->selectAll = false;

        session()->flash('success', 'Selected users deleted successfully.');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
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
        $this->selectAll = count($this->selectedUsers) === $this->totalUsersCount;
    }

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
     * Shared base query respecting the active role filter + search term.
     */
    protected function filteredQuery()
    {
        return User::role(['alumni', 'registrar', 'program head'])
            ->when($this->roleFilter !== 'all', function ($query) {
                $query->role($this->roleFilter);
            })
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            });
    }

    #[Computed]
    public function totalUsersCount()
    {
        return $this->filteredQuery()->count();
    }

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
