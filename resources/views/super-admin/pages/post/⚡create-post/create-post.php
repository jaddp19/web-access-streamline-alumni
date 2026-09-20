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

new #[Layout('layouts::app-super-admin')] class extends Component
{
    use WithFileUploads;

    public string $title = '';
    public string $description = '';
    public ?int $category_id = null;
    public string $status = 'draft';

    public $image = null;              // single image upload (nullable)
    public array $attachments = [];    // multiple file uploads

    public bool $showPreview = false;

    #[Computed]
    public function categories()
    {
        return Category::orderBy('cat_name')->get(['id', 'cat_name']);
    }

    #[Computed]
    public function slugPreview(): string
    {
        return Str::slug($this->title) ?: 'your-post-title';
    }

    protected function rules(): array
    {
        return [
            'title'         => 'required|string|min:3|max:255',
            'description'   => 'nullable|string|max:5000',
            'category_id'   => 'required|exists:categories,id',
            'status'        => 'required|in:public,private,draft',
            'image'         => 'nullable|image|max:5120',         // ← now nullable
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'        => 'Please enter a post title.',
            'title.min'             => 'Title must be at least 3 characters.',
            'description.max'       => 'Description cannot exceed 5000 characters.',
            'category_id.required'  => 'Please select a category.',
            'category_id.exists'    => 'The selected category no longer exists.',
            'image.image'           => 'Cover image must be a valid image file.',
            'image.max'             => 'Cover image cannot exceed 5MB.',
            'attachments.max'       => 'You can attach up to 5 files.',
            'attachments.*.max'     => 'Each attachment cannot exceed 10MB.',
        ];
    }

    public function updatedImage(): void
    {
        $this->validateOnly('image');
    }

    public function updatedAttachments(): void
    {
        $this->validateOnly('attachments');
    }

    public function removeAttachment(int $index): void
    {
        $items = $this->attachments;
        unset($items[$index]);
        $this->attachments = array_values($items);
    }

    public function removeImage(): void
    {
        $this->image = null;
    }

    public function save()
    {
        $validated = $this->validate();

        try {
            // Ensure unique slug
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $i = 2;
            while (Post::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$i}";
                $i++;
            }

            // Store cover image (optional)
            $imagePath = $this->image
                ? $this->image->store('posts', 'public')
                : null;

            // Store attachments
            $attachmentPaths = [];
            foreach ($this->attachments as $file) {
                if ($file) {
                    $attachmentPaths[] = $file->store('posts/attachments', 'public');
                }
            }

            Post::create([
                'user_id'     => Auth::id(),
                'title'       => trim($validated['title']),
                'description' => $validated['description'] ? trim($validated['description']) : null,
                'slug'        => $slug,
                'image'       => $imagePath,
                'category_id' => $validated['category_id'],
                'status'      => $validated['status'],
                'attachments' => $attachmentPaths,
            ]);

            session()->flash('success', 'Post created successfully.');

            return redirect()->route('super-admin.post.view');

        } catch (\Throwable $e) {
            logger()->error('Post creation failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace'   => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Something went wrong while creating the post: ' . $e->getMessage());
            return;
        }
    }
};