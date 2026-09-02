<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
    public ?int $category_id = null;
    public $image;

    public bool $composerOpen = false; // now tracked server-side so polling can respect it

    protected function rules()
    {
        return [
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'required|image|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'title.required'       => 'Please write something before posting.',
            'category_id.required' => 'Please choose a category.',
            'image.required'       => 'Please attach an image for this post.',
            'image.image'          => 'The file must be an image (JPG, PNG).',
            'image.max'            => 'The image must not be larger than 5MB.',
        ];
    }

    public function toggleComposer()
    {
        $this->composerOpen = ! $this->composerOpen;
    }

    public function closeComposer()
    {
        $this->composerOpen = false;
    }

    public function post()
    {
        $validated = $this->validate();

        $validated['title'] = $this->sanitizeData($validated['title']);

        $path = $this->image->store('post-images', 'public');

        Post::create([
            'user_id'     => Auth::id(),
            'title'       => $validated['title'],
            'slug'        => Str::slug($validated['title']) . '-' . Str::random(6),
            'image'       => $path,
            'category_id' => $validated['category_id'],
            'status'      => 'public',
            'attachments' => [],
        ]);

        $this->reset(['title', 'category_id', 'image']);
        $this->composerOpen = false;

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

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }

    #[Computed]
    public function posts()
    {
        return Post::with(['user.userProfile'])
            ->latest()
            ->paginate(6);
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('cat_name')->get();
    }

    #[Computed]
    public function myAvatarUrl()
    {
        $avatar = Auth::user()->userProfile?->avatar;

        return $avatar ? Storage::url($avatar) : null;
    }
};
