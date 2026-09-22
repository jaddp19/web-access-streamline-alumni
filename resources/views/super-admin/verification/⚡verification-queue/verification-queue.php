<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $rejectReasonInput = '';
    public ?int $rejectingUserId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // =========================================================
    //  SCOPE
    // =========================================================

    /**
     * null   → registrar (global)
     * int    → program head with a dept (scoped)
     * false  → program head with NO dept (sees nothing)
     */
    #[Computed]
    public function scope(): int|false|null
    {
        $user = Auth::user();
        if (! $user) return false;
        if ($user->hasRole('registrar')) return null;

        return Department::where('program_head_id', $user->id)->value('id') ?? false;
    }

    #[Computed]
    public function hasNoDepartment(): bool
    {
        return $this->scope === false;
    }

    #[Computed]
    public function myDepartmentName(): ?string
    {
        return is_int($this->scope)
            ? Department::whereKey($this->scope)->value('dept_name')
            : null;
    }

    // =========================================================
    //  PENDING USERS
    // =========================================================

    #[Computed]
    public function pendingUsers()
    {
        if ($this->scope === false) {
            return User::query()->whereRaw('1 = 0')->paginate(10);
        }

        $scope = $this->scope;

        return User::role('alumni')
            ->whereHas('userProfile', function ($q) use ($scope) {
                $q->where('is_verified', false)
                  ->whereNotNull('board_taken')
                  ->whereNotNull('board_rate')
                  ->whereHas('courses', function ($c) use ($scope) {
                      $c->where('course_type', 'board');

                      if (is_int($scope)) {
                          $c->where('department_id', $scope);
                      }
                  });
            })
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                          ->orWhere('email', 'like', $term)
                          ->orWhere('school_id', 'like', $term);
                });
            })
            ->with([
                'userProfile.batch:id,batch_name',
                'userProfile.courses:id,course_title,course_type,department_id',
                'userProfile.courses.department:id,dept_name',
            ])
            ->select('id', 'name', 'email', 'school_id', 'created_at')
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function rejectingUserName(): ?string
    {
        if (! $this->rejectingUserId) {
            return null;
        }
        return User::query()->whereKey($this->rejectingUserId)->value('name');
    }

    // =========================================================
    //  APPROVE
    // =========================================================

    public function approve(int $userId): void
    {
        $scope = $this->scope;
        if ($scope === false) {
            abort(403, 'You do not have a department assigned.');
        }

        $user = User::with('userProfile.courses:id,course_type,department_id')->find($userId);

        if (! $user) {
            session()->flash('status', 'User not found.');
            return;
        }

        if ($user->hasAnyRole(['program head', 'registrar'])) {
            abort(403, 'Cannot modify staff accounts from this queue.');
        }

        if (is_int($scope) && ! $this->userBelongsToDepartment($user, $scope)) {
            abort(403, 'This alumni does not belong to your department.');
        }

        $profile = $user->userProfile;

        if (
            ! $profile
            || ! $profile->board_taken
            || $profile->board_rate === null
            || ! $profile->courses->contains(fn ($c) => $c->course_type === 'board')
        ) {
            session()->flash(
                'status',
                "{$user->name} is not eligible for verification (missing board exam details or non-board program)."
            );
            return;
        }

        try {
            DB::transaction(function () use ($user, $profile) {
                // Lock the profile row to prevent double-approve race.
                $lockedProfile = $profile->newQuery()->whereKey($profile->id)->lockForUpdate()->first();

                if ($lockedProfile && $lockedProfile->is_verified) {
                    return; // already approved by another admin
                }

                $user->syncRoles(['alumni']);

                $lockedProfile?->update(['is_verified' => true]);
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('status', 'Could not approve user. Please try again.');
            return;
        }

        Cache::forget('verification:pending-count');

        session()->flash('status', "{$user->name} has been approved as a verified alumni.");
    }

    // =========================================================
    //  REJECT
    // =========================================================

    public function openRejectModal(int $userId): void
    {
        $this->rejectingUserId   = $userId;
        $this->rejectReasonInput = '';
        $this->resetErrorBag('rejectReasonInput');
    }

    public function closeRejectModal(): void
    {
        $this->rejectingUserId   = null;
        $this->rejectReasonInput = '';
        $this->resetErrorBag('rejectReasonInput');
    }

    public function confirmReject(): void
    {
        $scope = $this->scope;
        if ($scope === false) {
            abort(403, 'You do not have a department assigned.');
        }

        $this->validate([
            'rejectReasonInput' => ['nullable', 'string', 'max:500'],
        ]);

        if (! $this->rejectingUserId) {
            session()->flash('status', 'No user selected.');
            return;
        }

        $user = User::with('userProfile')->find($this->rejectingUserId);

        if (! $user) {
            session()->flash('status', 'User not found.');
            $this->closeRejectModal();
            return;
        }

        if ($user->hasAnyRole(['program head', 'registrar'])) {
            abort(403, 'Cannot modify staff accounts from this queue.');
        }

        if (is_int($scope) && ! $this->userBelongsToDepartment($user, $scope)) {
            abort(403, 'This alumni does not belong to your department.');
        }

        try {
            DB::transaction(function () use ($user) {
                // Keep them out of the verified pool.
                $user->userProfile?->update(['is_verified' => false]);
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('status', 'Could not reject user. Please try again.');
            return;
        }

        Cache::forget('verification:pending-count');

        session()->flash('status', "{$user->name}'s application was rejected.");

        $this->closeRejectModal();
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function userBelongsToDepartment(User $user, int $departmentId): bool
    {
        if (! $user->relationLoaded('userProfile')) {
            $user->load('userProfile.courses:id,course_type,department_id');
        }

        return $user->userProfile
            && $user->userProfile->courses->contains('department_id', $departmentId);
    }
};