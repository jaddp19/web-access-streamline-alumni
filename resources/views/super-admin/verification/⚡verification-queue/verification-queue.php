<?php

use App\Models\User;
use App\Models\UserProfile;
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

        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);

        $location = is_array($profile->location) ? $profile->location : [];
        unset($location['rejected_at'], $location['rejection_reason']);

        $profile->update([
            'is_verified' => true,
            'location'    => $location,
        ]);

        $user->syncRoles(['alumni']);

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

        $profile = UserProfile::firstOrCreate(['user_id' => $user->id]);

        $location = is_array($profile->location) ? $profile->location : [];

        $profile->update([
            'is_verified' => false,
            'location' => array_merge($location, [
                'rejected_at'      => now()->toDateTimeString(),
                'rejection_reason' => $this->rejectReasonInput ?: 'Could not be verified against school records.',
            ]),
        ]);

        // Rejected applicants keep the 'pending-verification' role so they
        // remain visible in this queue (with the rejection banner shown)
        // and can be re-reviewed if they update their info.

        session()->flash('status', "{$user->name}'s application was rejected.");

        $this->closeRejectModal();
    }

    public function with(): array
    {
        return [
            // "Pending" = user holds the 'pending-verification' role,
            // assigned at registration. This matches RoleSeeder and the
            // actual signup flow — NOT "no roles at all".
            'pendingUsers' => User::role('pending-verification')
                ->when($this->search, fn ($q) => $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                }))
                ->latest()
                ->paginate(8),
        ];
    }
};
