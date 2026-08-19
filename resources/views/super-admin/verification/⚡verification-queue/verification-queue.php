<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $viewingUserId = null;
    public string $rejectReasonInput = '';
    public ?int $rejectingUserId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function approve(User $user)
    {
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            abort(403, 'Cannot modify staff accounts from this queue.');
        }

        $user->update(['verification_status' => 'verified']);
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

        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            abort(403, 'Cannot modify staff accounts from this queue.');
        }

        $user->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $this->rejectReasonInput ?: 'Documents could not be verified.',
        ]);
        $user->syncRoles([]);

        session()->flash('status', "{$user->name}'s application was rejected.");

        $this->closeRejectModal();
    }

    public function documentUrl(User $user): ?string
    {
        if (! $user->verification_document) {
            return null;
        }

        return Storage::disk('private')->temporaryUrl(
            $user->verification_document,
            now()->addMinutes(10)
        );
    }

    public function with(): array
    {
        return [
            'pendingUsers' => User::where('verification_status', 'pending')
                ->whereDoesntHave('roles', function ($q) {
                    $q->whereIn('name', ['admin', 'super-admin']);
                })
                ->when($this->search, fn ($q) => $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                }))
                ->latest()
                ->paginate(8),
        ];
    }
};
