<?php

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    #[Computed]
    public function totalUsersCount()
    {
        // Count only users with no roles
        return User::doesntHave('roles')->count();
    }

    #[Computed]
    public function users()
    {
        // Query only users who have no roles
        return User::doesntHave('roles')
            ->select('id', 'name', 'email', 'created_at')
            ->latest()
            ->paginate(5);
    }

    public function accept(int $userId)
    {
        $user = User::findOrFail($userId);
        $user->assignRole('alumni'); // Spatie method

        session()->flash('success', "{$user->name} has been accepted as an alumni.");
    }

    public function decline(int $userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        session()->flash('success', "User {$user->name} has been deleted.");
    }
};
