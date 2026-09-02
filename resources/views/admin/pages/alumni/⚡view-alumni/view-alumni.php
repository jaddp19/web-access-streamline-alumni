<?php

use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-admin')] class extends Component
{
    use WithPagination;

    // TODO: There is currently no table linking an admin/program-head
    // user to a specific department (no `department_id` on users, no
    // admin_department pivot). Once that exists, filter the query below
    // by the logged-in admin's department instead of showing everyone.

    #[Computed]
    public function educationalBackgrounds()
    {
        return UserProfile::query()
            ->whereHas('user', fn ($q) => $q->role('alumni'))
            ->with(['user', 'batch', 'courses.department'])
            ->latest()
            ->paginate(5);
    }
};
