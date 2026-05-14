<?php

use App\Mail\DynamicEmail;
use App\Models\Email;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app-super-admin')] class extends Component
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

    }
    
    public function decline(int $userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();
        session()->flash('success', "User {$user->name} has been deleted.");
    }
};
