<?php

namespace App\Livewire\Alumni;

use App\Models\Event;
use App\Models\EventRsvp;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-alumni')] class extends Component
{
    public Event $event;

    public function mount(Event $event): void
    {
        abort_unless(
            in_array($event->status, ['published', 'completed'], true),
            404
        );

        $this->event = $event;
    }

    #[Computed]
    public function myRsvp(): ?EventRsvp
    {
        return EventRsvp::query()
            ->where('event_id', $this->event->id)
            ->where('user_id', Auth::id())
            ->first();
    }

    #[Computed]
    public function attendeeCount(): int
    {
        return $this->event->rsvps()->where('response', 'yes')->count();
    }

    #[Computed]
    public function canRsvp(): bool
    {
        if ($this->event->status !== 'published') return false;
        if ($this->event->isCancelled()) return false;
        if ($this->event->starts_at->isPast()) return false;

        if ($this->event->registration_deadline
            && now()->isAfter($this->event->registration_deadline)) {
            return false;
        }

        return true;
    }

    public function rsvp(string $response): void
    {
        abort_unless(in_array($response, ['yes', 'no', 'maybe'], true), 422);
        abort_unless($this->canRsvp, 403, 'RSVP is closed for this event.');

        if ($response === 'yes' && $this->event->capacity) {
            $existing = $this->myRsvp;
            $isAlreadyYes = $existing && $existing->response === 'yes';

            if (! $isAlreadyYes && $this->attendeeCount >= $this->event->capacity) {
                $this->addError('rsvp', 'This event has reached full capacity.');
                return;
            }
        }

        try {
            EventRsvp::updateOrCreate(
                ['event_id' => $this->event->id, 'user_id' => Auth::id()],
                ['response' => $response, 'responded_at' => now()]
            );

            unset($this->myRsvp, $this->attendeeCount);

            session()->flash('rsvp_success', match ($response) {
                'yes'   => "You're going! See you there.",
                'maybe' => 'Marked as maybe. You can update this anytime.',
                'no'    => "Response saved. We'll miss you!",
            });
        } catch (\Throwable $e) {
            report($e);
            $this->addError('rsvp', 'Could not save your response. Please try again.');
        }
    }

    public function cancelRsvp(): void
    {
        abort_unless($this->canRsvp, 403);

        EventRsvp::query()
            ->where('event_id', $this->event->id)
            ->where('user_id', Auth::id())
            ->delete();

        unset($this->myRsvp, $this->attendeeCount);

        session()->flash('rsvp_success', 'Your RSVP has been removed.');
    }
};