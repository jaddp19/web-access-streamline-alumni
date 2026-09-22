<?php

use App\Jobs\SendPostAnnouncementEmail;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-super-admin')] class extends Component
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

    /** Set by the "Resend announcement" checkbox — only applies to already-public posts. */
    public bool $resendAnnouncement = false;

    public function mount(Post $post): void
    {
        abort_unless(
            $post->user_id === Auth::id() || Auth::user()?->hasRole('registrar'),
            403
        );

        $this->post = $post;

        $this->title               = $post->title;
        $this->description         = $post->description ?? '';
        $this->category_id         = $post->category_id;
        $this->status              = $post->status;
        $this->currentImage        = $post->image;
        $this->existingAttachments = is_array($post->attachments) ? $post->attachments : [];
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
            'title'               => ['required', 'string', 'min:3', 'max:255'],
            'description'         => ['nullable', 'string', 'max:5000'],
            'category_id'         => ['required', 'integer', Rule::exists('categories', 'id')],
            'status'              => ['required', Rule::in(['public', 'private', 'draft'])],
            'image'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'attachments'         => ['nullable', 'array', 'max:5'],
            'attachments.*'       => [
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png,webp,txt,csv',
            ],
            'resendAnnouncement'  => ['boolean'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'        => 'Please enter a post title.',
            'title.min'             => 'Title must be at least 3 characters.',
            'title.max'             => 'Title cannot exceed 255 characters.',
            'description.max'       => 'Description cannot exceed 5000 characters.',
            'category_id.required'  => 'Please select a category.',
            'category_id.exists'    => 'The selected category no longer exists.',
            'status.in'             => 'The selected status is invalid.',
            'image.image'           => 'Cover image must be a valid image file.',
            'image.mimes'           => 'Cover image must be a JPG, PNG, or WebP file.',
            'image.max'             => 'Cover image cannot exceed 5MB.',
            'attachments.max'       => 'You can attach up to 5 files.',
            'attachments.*.max'     => 'Each attachment cannot exceed 10MB.',
            'attachments.*.mimes'   => 'One or more attachments have an unsupported file type.',
        ];
    }

    // =========================================================
    //  LIVE VALIDATION
    // =========================================================

    public function updatedImage(): void
    {
        $this->validateOnly('image');
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
        abort_unless(
            $this->post->user_id === Auth::id() || Auth::user()?->hasRole('registrar'),
            403
        );

        $validated = $this->validate();

        // Capture status BEFORE update — used to decide blast behavior.
        $wasPublic = $this->post->status === 'public';

        $newImagePath       = null;
        $newAttachmentPaths = [];

        try {
            // --- 1. Build the slug ---
            $newSlug = $this->post->slug;
            if (trim($validated['title']) !== $this->post->title) {
                $newSlug = $this->uniqueSlug($validated['title']);
            }

            // --- 2. Compute final image path ---
            $finalImagePath = $this->currentImage;

            if ($this->image) {
                $newImagePath   = $this->image->store('posts', 'public');
                $finalImagePath = $newImagePath;
            } elseif (! $this->currentImage) {
                $finalImagePath = null;
            }

            // --- 3. Compute final attachments list ---
            $finalAttachments = $this->existingAttachments;

            foreach ($this->attachments as $file) {
                if ($file) {
                    $path = $file->store('posts/attachments', 'public');
                    $newAttachmentPaths[] = $path;
                    $finalAttachments[]   = $path;
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
                    'title'       => trim(strip_tags($validated['title'])),
                    'description' => filled($validated['description'])
                        ? trim(strip_tags($validated['description']))
                        : null,
                    'slug'        => $newSlug,
                    'image'       => $finalImagePath,
                    'category_id' => $validated['category_id'],
                    'status'      => $validated['status'],
                    'attachments' => array_values($finalAttachments) ?: null,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $this->cleanupFiles($newImagePath, $newAttachmentPaths);

            if ($e->getCode() === '23000') {
                $this->addError('title', 'This title was just taken. Please try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not update post. Please try again.');
            return;
        } catch (\Throwable $e) {
            $this->cleanupFiles($newImagePath, $newAttachmentPaths);

            report($e);
            session()->flash('error', 'Could not update post. Please try again.');
            return;
        }

        // --- 5. Cleanup OLD files AFTER successful commit ---
        $originalImage = $this->post->getOriginal('image');
        if ($originalImage && $originalImage !== $finalImagePath) {
            if (! $this->isSharedFile($originalImage, 'image')) {
                Storage::disk('public')->delete($originalImage);
            }
        }

        $originalAttachments = is_array($this->post->getOriginal('attachments'))
            ? $this->post->getOriginal('attachments')
            : [];

        $removed = array_diff($originalAttachments, $this->existingAttachments);
        foreach ($removed as $path) {
            if ($path && ! $this->isSharedFile($path, 'attachment')) {
                Storage::disk('public')->delete($path);
            }
        }

        // --- 6. Email blast decision ---
        // Case A — first-time publish: draft/private → public.
        $justBecamePublic = ! $wasPublic && $validated['status'] === 'public';

        // Case B — already public, admin explicitly ticked "resend".
        $adminRequestedResend = $wasPublic
            && $validated['status'] === 'public'
            && $this->resendAnnouncement;

        $shouldBlast = $justBecamePublic || $adminRequestedResend;

        if ($shouldBlast) {
            SendPostAnnouncementEmail::dispatch($this->post->id)->afterCommit();
        }

        Cache::forget('posts:count');

        // Reset the checkbox so it doesn't stay ticked on next edit.
        $this->resendAnnouncement = false;

        session()->flash('success', match (true) {
            $justBecamePublic       => 'Post published. Alumni will be notified shortly.',
            $adminRequestedResend   => 'Post updated. Announcement re-sent to all alumni.',
            default                 => 'Post updated successfully.',
        });

        return redirect()->route('super-admin.post.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $i    = 2;

        while (
            Post::where('slug', $slug)
                ->where('id', '!=', $this->post->id)
                ->exists()
        ) {
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