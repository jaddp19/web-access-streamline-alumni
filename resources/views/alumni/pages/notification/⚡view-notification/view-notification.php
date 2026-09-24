<?php

use App\Models\Department;
use App\Models\Post;
use App\Models\PostRead;
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
            ->whereIn('user_id', $this->allowedAuthorIds)
            ->when($this->filter === 'unread', function ($q) {
                $q->whereDoesntHave('reads', fn ($r) => $r->where('user_id', $this->alumni->id));
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
        return BadgeCounts::unreadNotifications($this->alumni->id);
    }

    /** Which post IDs on the current page this alumni has already read. */
    #[Computed]
    public function readPostIds(): array
    {
        $postIds = collect($this->notifications->items())->pluck('id')->all();

        if (empty($postIds)) {
            return [];
        }

        return PostRead::where('user_id', $this->alumni->id)
            ->whereIn('post_id', $postIds)
            ->pluck('post_id')
            ->all();
    }

    #[Computed]
    public function myAvatarUrl(): ?string
    {
        $avatar = Auth::user()->userProfile?->avatar;

        return $avatar ? Storage::url($avatar) : null;
    }

    public function markAllAsRead(): void
    {
        $postIds = Post::query()
            ->where('status', 'public')
            ->whereIn('user_id', $this->allowedAuthorIds)
            ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $this->alumni->id))
            ->pluck('id');

        if ($postIds->isNotEmpty()) {
            $now = now();
            $rows = $postIds->map(fn ($id) => [
                'user_id' => $this->alumni->id,
                'post_id' => $id,
                'read_at' => $now,
            ])->all();

            PostRead::upsert($rows, ['user_id', 'post_id'], ['read_at']);
        }

        BadgeCounts::forgetFor($this->alumni->id);
        unset($this->unreadCount, $this->notifications, $this->readPostIds);

        $this->dispatch('badges:refresh');
    }
};