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

    public Post $post;

    public string $title = '';
    public string $description = '';
    public ?int $category_id = null;
    public string $status = 'draft';

    // Image — either the existing path or a new upload
    public $image = null;                    // new upload (nullable)
    public ?string $currentImage = null;     // existing storage path

    // Attachments — mix of existing paths + new uploads
    public array $existingAttachments = [];  // ['path1', 'path2']
    public array $attachments = [];          // new uploads

    public function mount(Post $post): void
    {
        $this->post = $post;

        $this->title               = $post->title;
        $this->description         = $post->description ?? '';
        $this->category_id         = $post->category_id;
        $this->status              = $post->status;
        $this->currentImage        = $post->image;
        $this->existingAttachments = is_array($post->attachments)
            ? $post->attachments
            : [];
    }

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

    #[Computed]
    public function allAttachmentsCount(): int
    {
        return count($this->existingAttachments) + count($this->attachments);
    }

    protected function rules(): array
    {
        return [
            'title'         => 'required|string|min:3|max:255',
            'description'   => 'nullable|string|max:5000',
            'category_id'   => 'required|exists:categories,id',
            'status'        => 'required|in:public,private,draft',
            'image'         => 'nullable|image|max:5120',
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

    public function removeImage(): void
    {
        $this->image = null;
    }

    public function removeExistingImage(): void
    {
        // Mark the existing image for deletion on save
        $this->currentImage = null;
    }

    public function removeAttachment(int $index): void
    {
        $items = $this->attachments;
        unset($items[$index]);
        $this->attachments = array_values($items);
    }

    public function removeExistingAttachment(int $index): void
    {
        $items = $this->existingAttachments;
        unset($items[$index]);
        $this->existingAttachments = array_values($items);
    }

    public function save()
    {
        $validated = $this->validate();

        try {
            // Slug — regenerate only if the title changed
            $newSlug = $this->post->slug;
            if (trim($validated['title']) !== $this->post->title) {
                $baseSlug = Str::slug($validated['title']);
                $slug = $baseSlug;
                $i = 2;
                while (
                    Post::where('slug', $slug)
                        ->where('id', '!=', $this->post->id)
                        ->exists()
                ) {
                    $slug = "{$baseSlug}-{$i}";
                    $i++;
                }
                $newSlug = $slug;
            }

            // ===== Handle image =====
            $imagePath = $this->currentImage;

            if ($this->image) {
                // New upload replaces any existing
                if ($this->post->image && Storage::disk('public')->exists($this->post->image)) {
                    Storage::disk('public')->delete($this->post->image);
                }

                $imagePath = $this->image->store('posts', 'public');
            } elseif (! $this->currentImage) {
                // User cleared the image without uploading a new one —
                // delete the old file from storage.
                if ($this->post->image && Storage::disk('public')->exists($this->post->image)) {
                    Storage::disk('public')->delete($this->post->image);
                }

                $imagePath = null;
            }

            // ===== Handle attachments =====
            $attachmentPaths = $this->existingAttachments;

            foreach ($this->attachments as $file) {
                if ($file) {
                    $attachmentPaths[] = $file->store('posts/attachments', 'public');
                }
            }

            // Delete any old files that were removed by the user
            $old = is_array($this->post->attachments) ? $this->post->attachments : [];
            $removed = array_diff($old, $this->existingAttachments);
            foreach ($removed as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }

            $this->post->update([
                'title'       => trim($validated['title']),
                'description' => $validated['description'] ? trim($validated['description']) : null,
                'slug'        => $newSlug,
                'image'       => $imagePath,
                'category_id' => $validated['category_id'],
                'status'      => $validated['status'],
                'attachments' => array_values($attachmentPaths),
            ]);

            session()->flash('success', 'Post updated successfully.');

            return redirect()->route('super-admin.post.view');

        } catch (\Throwable $e) {
            logger()->error('Post update failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'post_id' => $this->post->id,
                'trace'   => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Something went wrong while updating the post: ' . $e->getMessage());
            return;
        }
    }
};