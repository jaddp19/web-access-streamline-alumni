<?php

namespace App\Livewire\Alumni;

use App\Models\Department;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-alumni')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all'; // all | pending | responded

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        if (! in_array($filter, ['all', 'pending', 'responded'], true)) {
            return;
        }

        $this->filter = $filter;
        $this->resetPage();
    }

    #[Computed]
    public function alumni()
    {
        return Auth::user();
    }

    /** Registrar user IDs — cached, small, stable set. */
    #[Computed]
    public function registrarIds(): array
    {
        return Cache::remember('registrar_user_ids', now()->addHour(), function () {
            return User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', 'registrar'))
                ->pluck('id')
                ->all();
        });
    }

    /**
     * Program-head user IDs whose department contains this alumni.
     *
     * Uses the User->department() relation directly (hasOne via program_head_id),
     * so we don't rely on pivot column ambiguity or the user_profiles.courses pluck.
     */
    #[Computed]
    public function programHeadIdsForMyDepartments(): array
    {
        $profile = $this->alumni->userProfile;
        if (! $profile) {
            return [];
        }

        // Explicitly qualified — avoids ambiguity with the pivot table.
        $deptIds = $profile->courses()
            ->pluck('courses.department_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($deptIds)) {
            return [];
        }

        sort($deptIds);

        // v2 prefix so we never read stale data written by the old logic.
        // Shorter TTL — department assignments change more often than 1 hour.
        $key = 'program_head_ids_by_dept_v2_' . implode('_', $deptIds);

        return Cache::remember($key, now()->addMinutes(15), function () use ($deptIds) {
            return User::query()
                ->role('program head')
                ->whereHas('department', fn ($q) => $q->whereIn('id', $deptIds))
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        });
    }

    /** Who is allowed to send this alumni messages. */
    #[Computed]
    public function allowedAuthorIds(): array
    {
        return array_values(array_unique(array_merge(
            $this->registrarIds,
            $this->programHeadIdsForMyDepartments,
        )));
    }

    /**
     * Messages = published events from allowed authors.
     */
    #[Computed]
    public function messages()
    {
        if (empty($this->allowedAuthorIds)) {
            return Event::query()->whereRaw('1 = 0')->paginate(15);
        }

        return Event::query()
            ->select(['id', 'title', 'slug', 'description', 'created_at', 'created_by', 'starts_at'])
            ->with([
                'creator:id,name',
                'creator.userProfile:id,user_id,avatar',
            ])
            ->where('status', 'published')
            ->whereIn('created_by', $this->allowedAuthorIds)
            ->when($this->search, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('title', 'like', '%' . $this->search . '%')
                       ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter === 'pending', function ($q) {
                $q->whereDoesntHave('rsvps', fn ($qq) =>
                    $qq->where('user_id', $this->alumni->id)
                        ->whereIn('response', ['yes', 'maybe', 'no'])
                );
            })
            ->when($this->filter === 'responded', function ($q) {
                $q->whereHas('rsvps', fn ($qq) =>
                    $qq->where('user_id', $this->alumni->id)
                        ->whereIn('response', ['yes', 'maybe', 'no'])
                );
            })
            ->latest()
            ->paginate(15);
    }

    /** Count of un-responded messages — for the tab badge. */
    #[Computed]
    public function pendingCount(): int
    {
        if (empty($this->allowedAuthorIds)) {
            return 0;
        }

        return Event::query()
            ->where('status', 'published')
            ->whereIn('created_by', $this->allowedAuthorIds)
            ->whereDoesntHave('rsvps', fn ($q) =>
                $q->where('user_id', $this->alumni->id)
                    ->whereIn('response', ['yes', 'maybe', 'no'])
            )
            ->count();
    }

    /**
     * The alumni's RSVP response per event — scoped to the current page only.
     */
    #[Computed]
    public function myRsvps(): array
    {
        $eventIds = collect($this->messages->items())->pluck('id')->all();

        if (empty($eventIds)) {
            return [];
        }

        return EventRsvp::query()
            ->where('user_id', $this->alumni->id)
            ->whereIn('event_id', $eventIds)
            ->pluck('response', 'event_id')
            ->toArray();
    }
};