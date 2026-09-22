<?php

use App\Models\Department;
use App\Models\Post;
use App\Models\User;
use App\Support\BadgeCounts;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-alumni')] class extends Component
{
    use WithPagination;

    public string $filter = 'all'; // all | unread

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    #[Computed]
    public function alumni()
    {
        return Auth::user();
    }

    #[Computed]
    public function lastSeenAt()
    {
        return $this->alumni->last_seen_posts_at ?? $this->alumni->created_at;
    }

    /** Registrar user IDs — cached (small, stable set). */
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

    /** Department IDs this alumni belongs to (via student_course → courses). */
    #[Computed]
    public function alumniDepartmentIds(): array
    {
        $profile = $this->alumni->userProfile;
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

    /**
     * Program-head user IDs whose department contains this alumni.
     * Cached by department set — many alumni in the same dept share the entry.
     */
    #[Computed]
    public function programHeadIdsForMyDepartments(): array
    {
        $deptIds = $this->alumniDepartmentIds;
        if (empty($deptIds)) {
            return [];
        }

        sort($deptIds);
        $key = 'program_head_ids_by_dept_' . implode('_', $deptIds);

        return Cache::remember($key, now()->addHour(), function () use ($deptIds) {
            return Department::query()
                ->whereIn('id', $deptIds)
                ->whereNotNull('program_head_id')
                ->pluck('program_head_id')
                ->unique()
                ->values()
                ->all();
        });
    }

    /** The single source of truth: who can publish to this alumni. */
    #[Computed]
    public function allowedAuthorIds(): array
    {
        return array_values(array_unique(array_merge(
            $this->registrarIds,
            $this->programHeadIdsForMyDepartments,
        )));
    }

    protected function notificationsQuery()
    {
        return Post::with(['user.userProfile', 'user.roles', 'category'])
            ->where('status', 'public')
            ->whereIn('user_id', $this->allowedAuthorIds)   // ← replaces the nested whereHas
            ->when($this->filter === 'unread', function ($q) {
                $q->where('created_at', '>', $this->lastSeenAt);
            })
            ->latest();
    }

    #[Computed]
    public function notifications(): LengthAwarePaginator
    {
        if (empty($this->allowedAuthorIds)) {
            return new LengthAwarePaginator([], 0, 15);
        }

        return $this->notificationsQuery()->paginate(15);
    }

    #[Computed]
    public function unreadCount(): int
    {
        if (empty($this->allowedAuthorIds)) {
            return 0;
        }

        return Post::query()
            ->where('status', 'public')
            ->whereIn('user_id', $this->allowedAuthorIds)
            ->where('created_at', '>', $this->lastSeenAt)
            ->count();
    }

    #[Computed]
    public function myAvatarUrl(): ?string
    {
        $avatar = Auth::user()->userProfile?->avatar;

        return $avatar ? Storage::url($avatar) : null;
    }

    public function markAllAsRead(): void
    {
        Auth::user()->update(['last_seen_posts_at' => now()]);
        BadgeCounts::forgetFor(Auth::id());

        unset($this->unreadCount, $this->lastSeenAt, $this->notifications);

        $this->dispatch('badges:refresh'); // <-- the missing piece
    }
};