<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-admin')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = 'all';   // all | public | private | draft

    #[Url]
    public string $categoryFilter = 'all'; // all | {category_id}

    protected int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->categoryFilter = 'all';
        $this->resetPage();
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function categories(): array
    {
        $cacheKey = 'posts:categories:v1';
        $cached   = Cache::get($cacheKey);

        if (! is_array($cached)) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if ($cached === null) {
            $cached = Category::query()
                ->orderBy('cat_name')
                ->get(['id', 'cat_name'])
                ->map(fn ($c) => ['id' => (int) $c->id, 'name' => (string) $c->cat_name])
                ->all();

            Cache::put($cacheKey, $cached, now()->addMinutes(5));
        }

        return $cached;
    }

    #[Computed]
    public function posts()
    {
        return Post::query()
            ->with([
                'user:id,name',
                'user.userProfile:id,user_id,avatar',
                'user.roles:id,name',
                'category:id,cat_name',
            ])
            ->where('user_id', Auth::id())  // ✅ only own posts
            ->select('id', 'title', 'slug', 'image', 'attachments', 'status', 'user_id', 'category_id', 'created_at')
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->categoryFilter !== 'all', fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';

                $q->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', $term)
                          ->orWhere('description', 'like', $term)
                          ->orWhere('slug', 'like', $term)
                          ->orWhereHas('category', fn ($c) => $c->where('cat_name', 'like', $term));
                });
            })
            ->latest()
            ->paginate($this->perPage);
    }

    #[Computed]
    public function hasFilters(): bool
    {
        return $this->search !== ''
            || $this->statusFilter !== 'all'
            || $this->categoryFilter !== 'all';
    }

    // =========================================================
    //  ACTIONS
    // =========================================================

    public function deletePost(int $id): void
    {
        $post = Post::query()
            ->where('user_id', Auth::id())
            ->find($id);

        if (! $post) {
            session()->flash('error', 'Post not found or you do not have permission to delete it.');
            return;
        }

        $files = array_filter([
            $post->image,
            ...((array) ($post->attachments ?? [])),
        ]);

        try {
            DB::transaction(fn () => $post->delete());
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Failed to delete post. Please try again.');
            return;
        }

        // Delete files AFTER successful DB commit.
        foreach ($files as $file) {
            if (is_string($file) && ! filter_var($file, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($file);
            }
        }

        session()->flash('success', 'Post deleted successfully.');
    }

    public function toggleStatus(int $id): void
    {
        try {
            $newStatus = DB::transaction(function () use ($id) {
                $post = Post::query()
                    ->where('user_id', Auth::id())
                    ->lockForUpdate()
                    ->find($id);

                if (! $post) {
                    return null;
                }

                $post->update([
                    'status' => $post->status === 'public' ? 'draft' : 'public',
                ]);

                return $post->status;
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Failed to update status.');
            return;
        }

        if ($newStatus === null) {
            session()->flash('error', 'Post not found or you do not have permission to modify it.');
            return;
        }

        session()->flash('success', "Post marked as {$newStatus}.");
    }
};