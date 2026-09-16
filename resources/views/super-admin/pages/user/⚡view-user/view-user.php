<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $roleFilter = 'all';

    #[Url]
    public string $search = '';

    public $selectedUsers = [];
    public $selectAll = false;

    public function updatedRoleFilter()
    {
        $this->resetPage();
        $this->selectedUsers = [];
        $this->selectAll = false;
    }

    public function updatedSearch()
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
            ->with(['roles:id,name', 'tracerStudy:id,user_id'])
            ->select('id', 'name', 'email', 'created_at')
            ->latest()
            ->paginate(5);
    }

    /**
     * Human-readable tracer study status for a user.
     * Only meaningful for alumni — other roles return null.
     */
    protected function tracerStatusFor(User $user): ?string
    {
        if (! $user->hasRole('alumni')) {
            return null;
        }

        return $user->tracerStudy ? 'Completed' : 'Pending';
    }

    /**
     * Export the currently filtered/searched result set (respects
     * role tab + search box, ignores pagination — exports everything
     * matching, not just the current page).
     */
    public function exportFilteredCsv(): StreamedResponse
    {
        $users = $this->filteredQuery()
            ->with(['roles:id,name', 'tracerStudy:id,user_id'])
            ->select('id', 'name', 'email', 'school_id', 'created_at')
            ->latest()
            ->get();

        return $this->streamUsersAsCsv($users, 'users-filtered');
    }

    /**
     * Export only the checked rows.
     */
    public function exportSelectedCsv(): StreamedResponse
    {
        $users = User::role(['alumni', 'registrar', 'program head'])
            ->whereIn('id', $this->selectedUsers)
            ->with(['roles:id,name', 'tracerStudy:id,user_id'])
            ->select('id', 'name', 'email', 'school_id', 'created_at')
            ->latest()
            ->get();

        return $this->streamUsersAsCsv($users, 'users-selected');
    }

    protected function streamUsersAsCsv($users, string $filenamePrefix): StreamedResponse
    {
        $filename = $filenamePrefix . '-' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel doesn't mangle special characters
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['Name', 'Email', 'School ID', 'Roles', 'Tracer Study', 'Created At']);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->school_id ?? '',
                    $user->roles->pluck('name')->implode(', '),
                    $this->tracerStatusFor($user) ?? 'N/A',
                    $user->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
};
