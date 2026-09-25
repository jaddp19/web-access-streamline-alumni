<?php

use App\Jobs\SendEventCancellationEmail;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = 'all';   // all | draft | published | cancelled | completed

    #[Url]
    public string $timeFilter = 'upcoming'; // upcoming | past | all

    protected int $perPage = 15;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTimeFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->timeFilter = 'upcoming';
        $this->resetPage();
    }

    #[Computed]
    public function events()
    {
        return Event::query()
            ->with('creator:id,name')
            ->select('id', 'title', 'slug', 'image', 'starts_at', 'ends_at', 'location', 'capacity', 'status', 'created_by', 'created_at')
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->timeFilter === 'upcoming', fn ($q) => $q->where('starts_at', '>=', now()))
            ->when($this->timeFilter === 'past', fn ($q) => $q->where('starts_at', '<', now()))
            ->when($this->search !== '', function ($q) {
                $term = '%'.$this->search.'%';
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

    public function deleteEvent(int $id): void
    {
        abort_unless(Auth::user()?->hasAnyRole(['registrar']), 403);

        $event = Event::find($id);

        if (! $event) {
            session()->flash('error', 'Event not found.');

            return;
        }

        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        session()->flash('success', 'Event deleted successfully.');
    }

    public function cancelEvent(int $id): void
    {
        abort_unless(Auth::user()?->hasAnyRole(['registrar']), 403);

        $event = Event::find($id);

        if (! $event) {
            session()->flash('error', 'Event not found.');

            return;
        }

        if ($event->status === 'cancelled') {
            session()->flash('error', 'This event is already cancelled.');

            return;
        }

        $wasPublished = $event->status === 'published';

        $event->update(['status' => 'cancelled']);

        if ($wasPublished) {
            SendEventCancellationEmail::dispatch($event->id)->afterCommit();
        }

        session()->flash(
            'success',
            $wasPublished
                ? 'Event cancelled. Attendees have been notified.'
                : 'Event cancelled.'
        );
    }
};
