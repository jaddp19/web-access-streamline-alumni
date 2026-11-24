<?php

use App\Jobs\SendPostAnnouncementEmail;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-admin')] class extends Component
{
    use WithFileUploads;

    public Post $post;

    public string $title = '';

    public string $description = '';

    public ?int $category_id = null;

    public string $status = 'draft';

    public $image = null;

    public ?string $currentImage = null;

    public array $existingAttachments = [];

    public array $attachments = [];

    public bool $resendAnnouncement = false;

    public function mount(Post $post): void
    {
        Gate::authorize('can_update');

        $this->post = $post;
        $this->title = $post->title;
        $this->description = $post->description ?? '';
        $this->category_id = $post->category_id;
        $this->status = $post->status;
        $this->currentImage = $post->image;

        // Ensure it's strictly an array
        $this->existingAttachments = is_array($post->attachments) ? $post->attachments : [];
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function categories(): array
    {
        $cacheKey = 'posts:categories:v1';
        $cached = Cache::get($cacheKey);

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

            Cache::put($cacheKey, $cached, now()->addMinutes(10));
        }

        return $cached;
    }

    #[Computed]
    public function slugPreview(): string
    {
        return Str::slug($this->title) ?: 'your-post-title';
    }

    #[Computed]
    public function descriptionLength(): int
    {
        return mb_strlen($this->description);
    }

    #[Computed]
    public function allAttachmentsCount(): int
    {
        return count($this->existingAttachments) + count($this->attachments);
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'status' => ['required', Rule::in(['public', 'private', 'draft'])],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => [
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png,webp,txt,csv',
            ],
            'resendAnnouncement' => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'Please enter a post title.',
            'title.min' => 'Title must be at least 3 characters.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 5000 characters.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category no longer exists.',
            'status.in' => 'The selected status is invalid.',
            // 👇 UPDATED MESSAGES HERE
            'image.image' => 'Only image files can be uploaded.',
            'image.mimes' => 'Only .png, .webp, .jpeg, and .jpg files are allowed for the cover image.',
            'image.max' => 'Cover image cannot exceed 5MB.',
            'attachments.max' => 'You can attach up to 5 files total.',
            'attachments.*.max' => 'Each attachment cannot exceed 10MB.',
            'attachments.*.mimes' => 'One or more attachments have an unsupported file type.',
        ];
    }

    // =========================================================
    //  LIVE VALIDATION
    // =========================================================

    public function updatedImage(): void
    {
        if (! $this->image) {
            return;
        }

        try {
            $this->validateOnly('image', [
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ], [
                'image.image' => 'Only image files can be uploaded.',
                'image.mimes' => 'Only .png, .webp, .jpeg, and .jpg files are allowed for the cover image.',
                'image.max' => 'Cover image cannot exceed 5MB.',
            ]);
        } catch (ValidationException $e) {
            $this->reset('image');
            throw $e;
        }
    }

    public function updatedAttachments(): void
    {
        $this->validateOnly('attachments');
    }

    // =========================================================
    //  FILE MANAGEMENT
    // =========================================================

    public function removeImage(): void
    {
        $this->image = null;
        $this->resetErrorBag('image');
    }

    public function removeExistingImage(): void
    {
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

    // =========================================================
    //  SAVE
    // =========================================================

    public function save()
    {
        Gate::authorize('can_update');

        $validated = $this->validate();

        $wasPublic = $this->post->status === 'public';
        $newImagePath = null;
        $newAttachmentPaths = [];

        // Store the ORIGINAL paths before we modify anything
        $originalImage = $this->post->image;
        $originalAttachments = is_array($this->post->attachments) ? $this->post->attachments : [];

        try {
            // --- 1. Build the slug ---
            $newSlug = $this->post->slug;
            if (trim($validated['title']) !== $this->post->title) {
                $newSlug = $this->uniqueSlug($validated['title']);
            }

            // --- 2. Compute final image path ---
            $finalImagePath = $this->currentImage;
            if ($this->image) {
                $newImagePath = $this->image->store('posts', 'public');
                $finalImagePath = $newImagePath;
            } elseif (! $this->currentImage) {
                $finalImagePath = null;
            }

            // --- 3. Compute final attachments list ---
            // Start with what was kept from the original
            $finalAttachments = $this->existingAttachments;

            // Append the newly uploaded files
            foreach ($this->attachments as $file) {
                if ($file) {
                    $path = $file->store('posts/attachments', 'public');
                    $newAttachmentPaths[] = $path;
                    $finalAttachments[] = $path;
                }
            }

            // --- 4. Update DB inside a transaction ---
            DB::transaction(function () use (
                $validated,
                $newSlug,
                $finalImagePath,
                $finalAttachments
            ) {
                $this->post->update([
                    'title' => trim(strip_tags($validated['title'])),
                    'description' => filled($validated['description'])
                        ? trim(strip_tags($validated['description']))
                        : null,
                    'slug' => $newSlug,
                    'image' => $finalImagePath,
                    'category_id' => $validated['category_id'],
                    'status' => $validated['status'],
                    'attachments' => array_values(array_unique($finalAttachments)) ?: null,
                ]);
            });

        } catch (QueryException $e) {
            $this->cleanupFiles($newImagePath, $newAttachmentPaths);

            if ($e->getCode() === '23000') {
                $this->addError('title', 'This title was just taken. Please try again.');

                return;
            }

            report($e);
            session()->flash('error', 'Could not update post. Please try again.');

            return;
        } catch (Throwable $e) {
            $this->cleanupFiles($newImagePath, $newAttachmentPaths);
            report($e);
            session()->flash('error', 'Could not update post. Please try again.');

            return;
        }

        // --- 5. Safely Cleanup OLD files ---
        // Delete old image ONLY if a new one was uploaded (and it's not used elsewhere)
        if ($originalImage && $originalImage !== $finalImagePath) {
            if (! $this->isSharedFile($originalImage, 'image')) {
                Storage::disk('public')->delete($originalImage);
            }
        }

        // Delete old attachments ONLY if they were explicitly removed by the user
        $removedAttachments = array_diff($originalAttachments, $this->existingAttachments);
        foreach ($removedAttachments as $path) {
            if ($path && ! $this->isSharedFile($path, 'attachment')) {
                Storage::disk('public')->delete($path);
            }
        }

        // --- 6. Email blast decision ---
        $justBecamePublic = ! $wasPublic && $validated['status'] === 'public';
        $adminRequestedResend = $wasPublic && $validated['status'] === 'public' && $this->resendAnnouncement;

        if ($justBecamePublic || $adminRequestedResend) {
            SendPostAnnouncementEmail::dispatch($this->post->id, Auth::id())->afterCommit();
        }

        Cache::forget('posts:count');
        $this->resendAnnouncement = false;

        session()->flash('success', match (true) {
            $justBecamePublic => 'Post published. Alumni will be notified shortly.',
            $adminRequestedResend => 'Post updated. Announcement re-sent to all alumni.',
            default => 'Post updated successfully.',
        });

        return redirect()->route('admin.post.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $i = 2;

        while (Post::where('slug', $slug)->where('id', '!=', $this->post->id)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    protected function cleanupFiles(?string $imagePath, array $attachmentPaths): void
    {
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        foreach ($attachmentPaths as $path) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function isSharedFile(string $path, string $type): bool
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return true;
        }

        $query = Post::query()->where('id', '!=', $this->post->id);

        if ($type === 'image') {
            return $query->where('image', $path)->exists();
        }

        return $query->whereJsonContains('attachments', $path)->exists();
    }
};
