<?php

use App\Models\Department;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-admin')] class extends Component
{
    use WithPagination;

    #[Computed]
    public function educationalBackgrounds()
    {
        $user = Auth::user();

        if (! $user) {
            return UserProfile::query()->whereRaw('0=1')->paginate(5);
        }

        $query = UserProfile::query()
            ->whereHas('user', fn ($q) => $q->role('alumni'))
            ->with(['user', 'batch', 'courses.department']);

        if ($user->hasRole('registrar')) {
            // Registrar: no department restriction, sees all alumni.

        } elseif ($user->hasRole('program head')) {
            // Program head may be assigned to more than one department,
            // so we collect all of them rather than just the first match.
            $departmentIds = Department::where('program_head_id', $user->id)->pluck('id');

            // No department assigned yet — show nothing, not everything.
            if ($departmentIds->isEmpty()) {
                return UserProfile::query()->whereRaw('0=1')->paginate(5);
            }

            $query->whereHas('courses', fn ($q) => $q->whereIn('department_id', $departmentIds));

        } else {
            // Any other role: not authorized to view this list at all.
            return UserProfile::query()->whereRaw('0=1')->paginate(5);
        }

        return $query->latest()->paginate(5);
    }
};
