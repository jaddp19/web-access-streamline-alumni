<?php

use App\Models\Batch;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.app-admin')] class extends Component
{
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

        // If assignment stored in departments.program_head_id:
        $department = Department::where('program_head_id', $user->id)->first();
        return $department?->id;
    }

    #[Computed]
    public function isRegistrar(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('registrar');
    }

    #[Computed]
    public function myDepartmentName(): ?string
    {
        $departmentId = $this->resolveDepartmentId();
        return Department::find($departmentId)?->dept_name;
    }

    #[Computed]
    public function totalUsers()
    {
        return User::count();
    }

    #[Computed]
    public function totalRoles()
    {
        return Role::count();
    }

    #[Computed]
    public function totalAlumni()
    {
        $departmentId = $this->resolveDepartmentId();

        return User::role('alumni')
            ->when($departmentId, function ($query, $departmentId) {
                $query->whereHas('userProfile.courses', fn ($q) => $q->where('department_id', $departmentId));
            })
            ->count();
    }

    #[Computed]
    public function totalFaculty()
    {
        return User::role('program head')->count();
    }

    #[Computed]
    public function alumniByBatch()
    {
        $departmentId = $this->resolveDepartmentId();

        return Batch::withCount(['userProfiles' => function ($query) use ($departmentId) {
                $query->whereHas('user', fn ($q) => $q->role('alumni'));

                if ($departmentId) {
                    $query->whereHas('courses', fn ($q) => $q->where('department_id', $departmentId));
                }
            }])
            ->orderBy('batch_name')
            ->get()
            ->mapWithKeys(fn ($batch) => [$batch->batch_name => $batch->user_profiles_count]);
    }

    #[Computed]
    public function newAlumniLast6Months()
    {
        $departmentId = $this->resolveDepartmentId();
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));

        return $months->mapWithKeys(function ($month) use ($departmentId) {
            $count = User::role('alumni')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->when($departmentId, function ($query, $departmentId) {
                    $query->whereHas('userProfile.courses', fn ($q) => $q->where('department_id', $departmentId));
                })
                ->count();

            return [$month->format('M Y') => $count];
        });
    }
};
