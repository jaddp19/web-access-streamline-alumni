<?php

namespace App\Livewire\Alumni;

use App\Models\Department;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\Post;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-alumni')] class extends Component
{

    #[Computed]
    public function isApproved(): bool
    {
        return (bool) $this->userProfile?->is_approved;
    }

    /** Registered alumni that have been approved (shown in the sidebar). */
    #[Computed]
    public function totalApprovedAlumni(): int
    {
        return Cache::remember('alumni:approved-count', now()->addMinutes(5), function () {
            return User::role('alumni')
                ->whereHas('userProfile', fn ($q) => $q->where('is_approved', true))
                ->count();
        });
    }

    #[Computed]
    public function alumni()
    {
        return Auth::user();
    }

    #[Computed]
    public function userProfile()
    {
        return UserProfile::with('batch')
            ->where('user_id', $this->alumni->id)
            ->first();
    }

    /**
     * Department IDs this alumni belongs to (via student_course → courses).
     * Qualified column to avoid pivot ambiguity.
     */
    #[Computed]
    public function alumniDepartmentIds(): array
    {
        $profile = $this->userProfile;
        if (! $profile) {
            return [];
        }

        return $profile->courses()
            ->pluck('courses.department_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Program-head user IDs whose department contains this alumni.
     * Same versioned cache pattern as the messages component.
     */
    #[Computed]
    public function programHeadIdsForMyDepartments(): array
    {
        $deptIds = $this->alumniDepartmentIds;
        if (empty($deptIds)) {
            return [];
        }

        sort($deptIds);

        // Global version counter — bumped by DepartmentObserver / UserObserver
        // whenever a program-head assignment changes. Old keys age out via TTL.
        $version = Cache::get('program_head_ids_cache_version', 1);
        $key = "program_head_ids_v{$version}_".implode('_', $deptIds);

        return Cache::remember($key, now()->addMinutes(15), function () use ($deptIds) {
            return User::query()
                ->role('program head')
                ->whereHas('department', fn ($q) => $q->whereIn('id', $deptIds))
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        });
    }

    /** Alumni Coordinator user IDs — cached (small, stable, role-driven). */
    #[Computed]
    public function alumniCoordinatorIds(): array
    {
        return Cache::remember('alumni_coordinator_user_ids', now()->addHour(), function () {
            return User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', 'alumni coordinator'))
                ->pluck('id')
                ->all();
        });
    }

    #[Computed]
    public function upcomingEvents()
    {
        if (! $this->isApproved) {
            return collect();
        }   
        $allowedCreatorIds = array_values(array_unique(array_merge(
            $this->alumniCoordinatorIds,
            $this->programHeadIdsForMyDepartments,
        )));

        if (empty($allowedCreatorIds)) {
            return Event::query()->whereRaw('1 = 0')->get();
        }

        return Event::query()
            ->with(['creator:id,name'])
            ->where('status', 'published')
            ->where('starts_at', '>=', now())
            ->whereIn('created_by', $allowedCreatorIds)
            ->orderBy('starts_at')
            ->take(3)
            ->get([
                'id', 'title', 'slug', 'image', 'starts_at',
                'location', 'capacity', 'created_by',
            ]);
    }

    #[Computed]
    public function myEventRsvps(): array
    {
        if (! $this->isApproved) {
            return [];
        }
        return EventRsvp::query()
            ->where('user_id', $this->alumni->id)
            ->pluck('response', 'event_id')
            ->toArray();
    }

    #[Computed]
    public function avatarUrl()
    {
        if ($this->userProfile?->avatar) {
            return Storage::url($this->userProfile->avatar);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->alumni->name).'&background=D4A537&color=123524';
    }

    #[Computed]
    public function workHistories()
    {
        return WorkHistory::with('company')
            ->where('user_id', $this->alumni->id)
            ->orderByDesc('date_hired')
            ->get();
    }

    #[Computed]
    public function profileCompletion()
    {
        $profile = $this->userProfile;

        $steps = [
            'avatar' => filled($profile?->avatar),
            'location' => filled($profile?->location),
            'batch' => filled($profile?->batch_id),
            'work_history' => $this->workHistories->isNotEmpty(),
        ];

        $completed = count(array_filter($steps));
        $total = count($steps);

        return [
            'steps' => $steps,
            'completed' => $completed,
            'total' => $total,
            'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }

    #[Computed]
    public function recentPosts()
    {
        if (! $this->isApproved) {
            return collect();
        }
        $allowedAuthorIds = array_values(array_unique(array_merge(
            $this->alumniCoordinatorIds,
            $this->programHeadIdsForMyDepartments,
        )));

        if (empty($allowedAuthorIds)) {
            return Post::query()->whereRaw('1 = 0')->get();
        }

        return Post::with(['user.userProfile', 'user.roles', 'category'])
            ->where('status', 'public')
            ->whereIn('user_id', $allowedAuthorIds)
            ->latest()
            ->take(3)
            ->get();
    }
};
