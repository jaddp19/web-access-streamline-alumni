<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-admin')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $rejectReasonInput = '';
    public ?int $rejectingUserId = null;

    /**
     * null   → registrar (global)
     * int    → program head with a dept (scoped)
     * false  → program head with NO dept (sees nothing)
     */
    protected function scopeDepartmentId(): int|false|null
    {
        $user = Auth::user();
        if (! $user) return false;
        if ($user->hasRole('registrar')) return null;

        $dept = Department::where('program_head_id', $user->id)->first();
        return $dept?->id ?? false;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function approve(int $userId)
    {
        $scope = $this->scopeDepartmentId();
        if ($scope === false) {
            abort(403, 'You do not have a department assigned.');
        }

        $user = User::findOrFail($userId);

        if ($user->hasAnyRole(['program head', 'registrar'])) {
            abort(403, 'Cannot modify staff accounts from this queue.');
        }

        // Program head can only approve alumni from their own department
        if (is_int($scope) && ! $this->userBelongsToDepartment($user, $scope)) {
            abort(403, 'This alumni does not belong to your department.');
        }

        $profile = $user->userProfile;

        if (! $profile
            || ! $profile->board_taken
            || $profile->board_rate === null
            || ! $profile->courses()->where('course_type', 'board')->exists()
        ) {
            session()->flash('status', "{$user->name} is not eligible for verification (missing board exam details or non-board program).");
            return;
        }

        // Ensure they hold the alumni role
        $user->syncRoles(['alumni']);

        // Clear any prior rejection state + mark as approved
        $user->update([
            'verification_status' => 'approved',
            'rejection_reason'    => null,
            'rejected_at'         => null,
        ]);

        // Mark profile as verified — removes them from this queue
        $profile->update([
            'is_verified' => true,
        ]);

        session()->flash('status', "{$user->name} has been approved as a verified alumni.");
    }

    public function openRejectModal(int $userId)
    {
        $this->rejectingUserId = $userId;
        $this->rejectReasonInput = '';
    }

    public function closeRejectModal()
    {
        $this->rejectingUserId = null;
        $this->rejectReasonInput = '';
    }

    public function confirmReject()
    {
        $scope = $this->scopeDepartmentId();
        if ($scope === false) {
            abort(403, 'You do not have a department assigned.');
        }

        $user = User::findOrFail($this->rejectingUserId);

        if ($user->hasAnyRole(['program head', 'registrar'])) {
            abort(403, 'Cannot modify staff accounts from this queue.');
        }

        if (is_int($scope) && ! $this->userBelongsToDepartment($user, $scope)) {
            abort(403, 'This alumni does not belong to your department.');
        }

        $user->update([
            'verification_status' => 'rejected',
            'rejection_reason'    => $this->rejectReasonInput ?: 'Could not be verified against school records.',
            'rejected_at'         => now(),
        ]);

        if ($user->userProfile) {
            $user->userProfile->update(['is_verified' => false]);
        }

        session()->flash('status', "{$user->name}'s application was rejected.");

        $this->closeRejectModal();
    }

    /**
     * Check whether a given alumni belongs to the scoped department.
     */
    protected function userBelongsToDepartment(User $user, int $departmentId): bool
    {
        return $user->userProfile
            && $user->userProfile->courses()
                ->where('department_id', $departmentId)
                ->exists();
    }

    public function with(): array
    {
        $scope = $this->scopeDepartmentId();

        // Program head with no dept → empty query
        if ($scope === false) {
            return [
                'pendingUsers' => User::query()->whereRaw('1 = 0')->paginate(10),
            ];
        }

        return [
            'pendingUsers' => User::role('alumni')
                ->whereHas('userProfile', function ($q) use ($scope) {
                    $q->where('is_verified', false)
                      ->whereNotNull('board_taken')
                      ->whereNotNull('board_rate')
                      ->whereHas('courses', function ($c) use ($scope) {
                          $c->where('course_type', 'board');

                          // Scope to the program head's department
                          if (is_int($scope)) {
                              $c->where('department_id', $scope);
                          }
                      });
                })
                ->when($this->search, fn ($q) => $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhere('school_id', 'like', "%{$this->search}%");
                }))
                ->with([
                    'userProfile.batch',
                    'userProfile.courses.department',
                ])
                ->latest()
                ->paginate(10),
        ];
    }
};