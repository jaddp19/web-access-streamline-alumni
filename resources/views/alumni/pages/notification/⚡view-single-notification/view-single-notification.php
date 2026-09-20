<?php

use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-alumni')] class extends Component
{
    public Post $post;

    public function mount(Post $post): void
    {
        // Only allow viewing public posts by registrar or program head
        abort_unless($post->status === 'public', 404);

        abort_unless(
            $post->user->hasAnyRole(['registrar', 'program head']),
            404
        );

        $this->post = $post->load(['user.userProfile', 'user.roles', 'category']);
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
            : \Illuminate\Support\Facades\Storage::url($raw);
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
            : \Illuminate\Support\Facades\Storage::url($this->post->image);
    }

    /**
     * Attachments as an array of ['url' => ..., 'name' => ..., 'ext' => ...]
     */
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
                    : \Illuminate\Support\Facades\Storage::url($path);

                $name = basename($path);
                $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));

                return [
                    'url'  => $url,
                    'name' => $name,
                    'ext'  => $ext,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Other recent posts for "More from CSAV" section.
     */
    #[Computed]
    public function relatedPosts()
    {
        return Post::with(['user.roles', 'category'])
            ->where('id', '!=', $this->post->id)
            ->where('status', 'public')
            ->whereHas('user', function ($q) {
                $q->whereHas('roles', fn ($r) => $r->whereIn('name', ['registrar', 'program head']));
            })
            ->latest()
            ->take(3)
            ->get();
    }
};