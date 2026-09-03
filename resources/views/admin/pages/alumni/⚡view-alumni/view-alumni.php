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

    protected function resolveDepartmentId(): ?int
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        // Registrar sees everything
        if ($user->hasRole('registrar')) {
            return null;
        }

        // Program head assignment stored in departments.program_head_id
        $department = Department::where('program_head_id', $user->id)->first();

        return $department?->id;
    }

    #[Computed]
    public function educationalBackgrounds()
    {
        $user = Auth::user();

        if (! $user) {
            return UserProfile::query()->whereRaw('0=1')->paginate(5);
        }

        // Registrar or Program Head are the only roles allowed to view this list
        if (! $user->hasRole('registrar') && ! $user->hasRole('program head')) {
            return UserProfile::query()->whereRaw('0=1')->paginate(5);
        }

        $departmentId = $this->resolveDepartmentId();

        return UserProfile::query()
            ->whereHas('user', fn ($q) => $q->role('alumni'))
            ->when($departmentId, function ($query, $departmentId) {
                $query->whereHas('courses', fn ($q) => $q->where('department_id', $departmentId));
            })
            ->with(['user', 'batch', 'courses.department'])
            ->latest()
            ->paginate(5);
    }
};