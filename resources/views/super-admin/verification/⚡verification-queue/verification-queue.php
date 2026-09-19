<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $rejectReasonInput = '';
    public ?int $rejectingUserId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function approve(int $userId)
    {
        $user = User::findOrFail($userId);

        if ($user->hasAnyRole(['program head', 'registrar'])) {
            abort(403, 'Cannot modify staff accounts from this queue.');
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
        $user = User::findOrFail($this->rejectingUserId);

        if ($user->hasAnyRole(['program head', 'registrar'])) {
            abort(403, 'Cannot modify staff accounts from this queue.');
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

    public function with(): array
    {
        return [
            'pendingUsers' => User::role('alumni')
                ->whereHas('userProfile', function ($q) {
                    $q->where('is_verified', false)
                      ->whereNotNull('board_taken')
                      ->whereNotNull('board_rate')
                      ->whereHas('courses', fn ($c) => $c->where('course_type', 'board'));
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
                ->paginate(10),   // ← changed from 5 to 10
        ];
    }
};