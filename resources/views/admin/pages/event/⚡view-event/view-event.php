<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-admin')] class extends Component
{
    use WithPagination;

    #[Url] public string $search = '';
    #[Url] public string $statusFilter = 'all';   // all | draft | published | cancelled | completed
    #[Url] public string $timeFilter = 'upcoming'; // upcoming | past | all

    protected int $perPage = 15;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingTimeFilter(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->timeFilter = 'upcoming';
        $this->resetPage();
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    /** True when the current user is a Program Head — scopes them to their own events. */
    #[Computed]
    public function isProgramHead(): bool
    {
        return Auth::user()?->hasRole('program head') ?? false;
    }

    #[Computed]
    public function events()
    {
        $user = Auth::user();

        return Event::query()
            ->with('creator:id,name')
            ->select('id', 'title', 'slug', 'image', 'starts_at', 'ends_at', 'location', 'capacity', 'status', 'created_by', 'created_at')
            // Program heads only see events they created themselves.
            ->when($this->isProgramHead, fn ($q) => $q->where('created_by', $user->id))
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->timeFilter === 'upcoming', fn ($q) => $q->where('starts_at', '>=', now()))
            ->when($this->timeFilter === 'past', fn ($q) => $q->where('starts_at', '<', now()))
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', $term)
                          ->orWhere('location', 'like', $term)
                          ->orWhere('description', 'like', $term);
                });
            })
            ->orderByDesc('starts_at')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function hasFilters(): bool
    {
        return $this->search !== ''
            || $this->statusFilter !== 'all'
            || $this->timeFilter !== 'upcoming';
    }

    // =========================================================
    //  DELETE
    // =========================================================

    public function deleteEvent(int $id): void
    {
        $user = Auth::user();

        abort_unless($user?->hasAnyRole(['registrar', 'program head']), 403);

        $event = Event::find($id);

        if (! $event) {
            session()->flash('error', 'Event not found.');
            return;
        }

        // Program heads can only delete their own events.
        if ($user->hasRole('program head') && $event->created_by !== $user->id) {
            abort(403, 'You can only delete your own events.');
        }

        if ($event->image && Storage::disk('public')->exists($event->image)) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        session()->flash('success', 'Event deleted successfully.');
    }
};