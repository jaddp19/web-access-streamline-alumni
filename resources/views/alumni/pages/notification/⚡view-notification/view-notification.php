<?php

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
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

    protected function notificationsQuery()
    {
        return Post::with(['user.userProfile', 'user.roles', 'category'])
            ->where('status', 'public')
            ->whereHas('user', function ($q) {
                $q->whereHas('roles', fn ($r) => $r->whereIn('name', ['registrar', 'program head']));
            })
            ->when($this->filter === 'unread', function ($q) {
                $q->where('created_at', '>', $this->lastSeenAt);
            })
            ->latest();
    }

    #[Computed]
    public function notifications()
    {
        return $this->notificationsQuery()->paginate(15);
    }

    #[Computed]
    public function unreadCount(): int
    {
        return Post::where('status', 'public')
            ->where('created_at', '>', $this->lastSeenAt)
            ->whereHas('user', function ($q) {
                $q->whereHas('roles', fn ($r) => $r->whereIn('name', ['registrar', 'program head']));
            })
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

        unset($this->unreadCount, $this->lastSeenAt);
    }

    
};