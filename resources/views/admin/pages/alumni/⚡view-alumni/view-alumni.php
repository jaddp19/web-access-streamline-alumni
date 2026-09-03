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
        $user = Auth::user();

        // If super-admin, show all alumni
        if ($user->hasRole('super-admin')) {
            return UserProfile::query()
                ->whereHas('user', fn ($q) => $q->role('alumni'))
                ->with(['user', 'batch', 'courses.department'])
                ->latest()
                ->paginate(5);
        }

        // If program head, filter by their department
        if ($user->hasRole('program head') && $user->headedDepartment) {
            $deptId = $user->headedDepartment->id;

            return UserProfile::query()
                ->whereHas('courses.department', fn ($q) => $q->where('id', $deptId))
                ->whereHas('user', fn ($q) => $q->role('alumni'))
                ->with(['user', 'batch', 'courses.department'])
                ->latest()
                ->paginate(5);
        }

        // Default: empty result
        return UserProfile::query()->whereRaw('0=1')->paginate(5);
    }

};
