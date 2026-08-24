<?php

use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

new #[Layout('layouts.app-alumni')] class extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $title = '';
    public string $description = '';
    public $image;

    // Per-post comment drafts: [postId => commentBody]
    public array $commentDrafts = [];

    protected function rules()
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'image'       => 'nullable|image|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Please write something before posting.',
            'image.image'    => 'The file must be an image (JPG, PNG).',
            'image.max'      => 'The image must not be larger than 5MB.',
        ];
    }

    public function post()
    {
        $validated = $this->validate();

        $validated['title']       = $this->sanitizeData($validated['title']);
        $validated['description'] = $this->sanitizeData($validated['description']);

        $path = $this->image ? $this->image->store('post-images', 'public') : null;

        Post::create([
            'title'       => $validated['title'],
            'user_id'     => Auth::id(),
            'description' => $validated['description'],
            'image'       => $path,
        ]);

        $this->reset(['title', 'description', 'image']);
        session()->flash('success', 'Posted!');
    }

    public function deletePost($postId)
    {
        $post = Post::find($postId);

        if ($post && $post->user_id === Auth::id()) {
            $post->delete();
            session()->flash('success', 'Post deleted.');
        }
    }

    public function addComment($postId)
    {
        $body = trim($this->commentDrafts[$postId] ?? '');

        if ($body === '') {
            return;
        }

        $post = Post::find($postId);
        if (! $post) {
            return;
        }

        PostComment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'body'    => Str::of($body)->stripTags()->trim()->toString(),
        ]);

        $this->commentDrafts[$postId] = '';
    }

    public function deleteComment($commentId)
    {
        $comment = PostComment::find($commentId);

        if ($comment && $comment->user_id === Auth::id()) {
            $comment->delete();
        }
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }

    #[Computed]
    public function posts()
    {
        return Post::with(['user', 'comments.user'])
            ->withCount('comments')
            ->latest()
            ->paginate(6);
    }
};
