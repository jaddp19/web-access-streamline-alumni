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

    /** Department IDs this alumni belongs to (via student_course → courses). */
    #[Computed]
    public function alumniDepartmentIds(): array
    {
        $profile = $this->userProfile;
        if (! $profile) {
            return [];
        }

        return $profile->courses()
            ->pluck('department_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /** Program-head user IDs whose department contains this alumni. */
    #[Computed]
    public function programHeadIdsForMyDepartments(): array
    {
        $deptIds = $this->alumniDepartmentIds;
        if (empty($deptIds)) {
            return [];
        }

        return Department::query()
            ->whereIn('id', $deptIds)
            ->whereNotNull('program_head_id')
            ->pluck('program_head_id')
            ->unique()
            ->values()
            ->all();
    }

    /** Registrar user IDs — cached (small, stable, role-driven). */
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

    #[Computed]
    public function upcomingEvents()
    {
        $allowedCreatorIds = array_values(array_unique(array_merge(
            $this->registrarIds,
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

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->alumni->name) . '&background=D4A537&color=123524';
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
            'avatar'       => filled($profile?->avatar),
            'location'     => filled($profile?->location),
            'batch'        => filled($profile?->batch_id),
            'work_history' => $this->workHistories->isNotEmpty(),
        ];

        $completed = count(array_filter($steps));
        $total     = count($steps);

        return [
            'steps'     => $steps,
            'completed' => $completed,
            'total'     => $total,
            'percent'   => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }

    #[Computed]
    public function recentPosts()
    {
        $allowedAuthorIds = array_values(array_unique(array_merge(
            $this->registrarIds,
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