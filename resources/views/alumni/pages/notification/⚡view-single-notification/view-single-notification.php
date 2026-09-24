<?php

use App\Models\Department;
use App\Models\Post;
use App\Models\PostRead;
use App\Models\User;
use App\Support\BadgeCounts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-alumni')] class extends Component
{
    public Post $post;

    public function mount(Post $post): void
    {
        abort_unless($this->isAuthorizedFor($post), 403);

        $this->post = $post;

        $user = Auth::user();

        $read = PostRead::firstOrCreate(
            ['user_id' => $user->id, 'post_id' => $post->id],
            ['read_at' => now()],
        );

        if ($read->wasRecentlyCreated) {
            BadgeCounts::forgetFor($user->id);
            $this->dispatch('badges:refresh');
        }
    }

    protected function isAuthorizedFor(Post $post): bool
    {
        if ($post->status !== 'public') {
            return false;
        }

        $registrarIds = Cache::remember('registrar_user_ids', now()->addHour(), function () {
            return User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', 'registrar'))
                ->pluck('id')
                ->all();
        });

        if (in_array($post->user_id, $registrarIds)) {
            return true;
        }

        return in_array($post->user_id, $this->programHeadIdsForMyDepartments());
    }

    protected function programHeadIdsForMyDepartments(): array
    {
        $profile = Auth::user()?->userProfile;
        if (! $profile) {
            return [];
        }

        $deptIds = $profile->courses()
            ->pluck('department_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

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
    public function authorInitial(): string
    {
        return strtoupper(substr($this->post->user->name ?? '?', 0, 1));
    }

    #[Computed]
    public function authorAvatarUrl(): ?string
    {
        $raw = $this->post->user?->userProfile?->avatar;

        if (! $raw) {
            return null;
        }

        return filter_var($raw, FILTER_VALIDATE_URL)
            ? $raw
            : Storage::url($raw);
    }

    #[Computed]
    public function authorRole(): ?string
    {
        return $this->post->user?->roles->first()?->name;
    }

    #[Computed]
    public function coverImageUrl(): ?string
    {
        if (! $this->post->image) {
            return null;
        }

        return filter_var($this->post->image, FILTER_VALIDATE_URL)
            ? $this->post->image
            : Storage::url($this->post->image);
    }

    #[Computed]
    public function attachmentList(): array
    {
        $raw = $this->post->attachments;

        if (! is_array($raw) || empty($raw)) {
            return [];
        }

        return collect($raw)
            ->filter()
            ->map(function ($path) {
                $url = filter_var($path, FILTER_VALIDATE_URL)
                    ? $path
                    : Storage::url($path);

                return [
                    'url'  => $url,
                    'name' => basename($path),
                    'ext'  => strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                ];
            })
            ->values()
            ->all();
    }

    #[Computed]
    public function relatedPosts()
    {
        $registrarIds = Cache::remember('registrar_user_ids', now()->addHour(), function () {
            return User::query()
                ->whereHas('roles', fn ($q) => $q->where('name', 'registrar'))
                ->pluck('id')
                ->all();
        });

        $allowed = array_values(array_unique(array_merge(
            $registrarIds,
            $this->programHeadIdsForMyDepartments(),
        )));

        if (empty($allowed)) {
            return collect();
        }

        return Post::with(['user.roles', 'category'])
            ->where('id', '!=', $this->post->id)
            ->where('status', 'public')
            ->whereIn('user_id', $allowed)
            ->latest()
            ->take(3)
            ->get();
    }
};