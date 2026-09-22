<?php

namespace App\Livewire\Alumni;

use App\Models\Department;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use App\Support\BadgeCounts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-alumni')] class extends Component
{
    public Event $event;

    public string $response = '';

    public function mount(Event $event): void
    {
        // Route model binding gives us the event, but we must still verify access
        abort_unless($this->isAuthorizedFor($event), 403);

        $this->event = $event;

        $this->response = EventRsvp::where('user_id', Auth::id())
            ->where('event_id', $event->id)
            ->value('response') ?? '';
    }

    protected function isAuthorizedFor(Event $event): bool
    {
        // Only published events are visible/RSVP-able to alumni
        if ($event->status !== 'published') {
            return false;
        }

        $registrarIds = Cache::remember('registrar_user_ids', now()->addHour(), function () {
            return User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', 'registrar'))
                ->pluck('id')
                ->all();
        });

        if (in_array($event->created_by, $registrarIds)) {
            return true;
        }

        $profile = Auth::user()->userProfile;
        if (! $profile) {
            return false;
        }

        $deptIds = $profile->courses()
            ->pluck('department_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($deptIds)) {
            return false;
        }

        $programHeadIds = Department::query()
            ->whereIn('id', $deptIds)
            ->whereNotNull('program_head_id')
            ->pluck('program_head_id')
            ->all();

        return in_array($event->created_by, $programHeadIds);
    }

    #[Computed]
    public function creator()
    {
        return $this->event->creator()->with('userProfile', 'roles')->first();
    }

    #[Computed]
    public function myRsvp(): ?EventRsvp
    {
        return EventRsvp::where('user_id', Auth::id())
            ->where('event_id', $this->event->id)
            ->first();
    }

    #[Computed]
    public function attendeeCount(): int
    {
        return EventRsvp::where('event_id', $this->event->id)
            ->where('response', 'yes')
            ->count();
    }

    public function rsvp(string $response): void
    {
        if (! in_array($response, ['yes', 'maybe', 'no'], true)) {
            return;
        }

        // Defense in depth — re-check status + authorization even though
        // mount() already gated the page itself.
        if ($this->event->status !== 'published' || ! $this->isAuthorizedFor($this->event)) {
            abort(403);
        }

        EventRsvp::updateOrCreate(
            ['user_id' => Auth::id(), 'event_id' => $this->event->id],
            ['response' => $response],
        );

        $this->response = $response;

        BadgeCounts::forgetFor(Auth::id());
        unset($this->myRsvp, $this->attendeeCount);

        $this->dispatch('badges:refresh');
    }
};