<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app-super-admin')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = 'all';   // all | public | private | draft

    #[Url]
    public string $categoryFilter = 'all'; // all | {category_id}

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

    #[Computed]
    public function categories()
    {
        return Category::orderBy('cat_name')->get(['id', 'cat_name']);
    }

    protected function filteredQuery()
    {
        return Post::with(['user.userProfile', 'user.roles', 'category'])
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->categoryFilter !== 'all', fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->search !== '', function ($q) {
                $q->where(function ($inner) {
                    $inner->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhere('slug', 'like', '%' . $this->search . '%')
                          ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . $this->search . '%'))
                          ->orWhereHas('category', fn ($c) => $c->where('cat_name', 'like', '%' . $this->search . '%'));
                });
            })
            ->latest();
    }

    #[Computed]
    public function posts()
    {
        return $this->filteredQuery()->paginate(10);
    }

    public function deletePost(int $id): void
    {
        try {
            $post = Post::findOrFail($id);

            // Delete cover image
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }

            // Delete attachments
            if (is_array($post->attachments)) {
                foreach ($post->attachments as $file) {
                    if ($file && Storage::disk('public')->exists($file)) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }

            $post->delete();

            session()->flash('success', 'Post deleted successfully.');
        } catch (\Throwable $e) {
            logger()->error('Post delete failed: ' . $e->getMessage(), ['id' => $id]);
            session()->flash('error', 'Failed to delete post: ' . $e->getMessage());
        }
    }

    public function toggleStatus(int $id): void
    {
        try {
            $post = Post::findOrFail($id);
            $post->update([
                'status' => $post->status === 'public' ? 'draft' : 'public',
            ]);
            session()->flash('success', "Post marked as {$post->status}.");
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to update status.');
        }
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->categoryFilter = 'all';
        $this->resetPage();
    }
};