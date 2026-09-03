<?php

use App\Models\Batch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.app-admin')] class extends Component
{
    /**
     * Resolve the logged-in admin's department_id for scoping.
     * Returns null for super-admin (unscoped / sees everything)
     * and for admins with no department assigned yet.
     */
    protected function resolveDepartmentId(): ?int
    {
        $user = Auth::user();

        if (! $user || (method_exists($user, 'hasRole') && $user->hasRole('registrar'))) {
            return null;
        }

        return $user->department_id ?? null;
    }

    #[Computed]
    public function isSuperAdmin(): bool
    {
        $user = Auth::user();

        return $user && method_exists($user, 'hasRole') && $user->hasRole('registrar');
    }

    #[Computed]
    public function myDepartmentName(): ?string
    {
        return Auth::user()?->department?->dept_name;
    }

    #[Computed]
    public function totalUsers()
    {
        // Only meaningful institution-wide, so only shown for super-admin (see view).
        return User::count();
    }

    #[Computed]
    public function totalRoles()
    {
        // Only meaningful institution-wide, so only shown for super-admin (see view).
        return Role::count();
    }

    #[Computed]
    public function totalAlumni()
    {
        $departmentId = $this->resolveDepartmentId();

        return User::role('alumni')
            ->when($departmentId, function ($query, $departmentId) {
                $query->whereHas('profile.courses', fn ($q) => $q->where('department_id', $departmentId));
            })
            ->count();
    }

    #[Computed]
    public function totalFaculty()
    {
        // Only meaningful institution-wide, so only shown for super-admin (see view).
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
                    $query->whereHas('profile.courses', fn ($q) => $q->where('department_id', $departmentId));
                })
                ->count();

            return [$month->format('M Y') => $count];
        });
    }
};
