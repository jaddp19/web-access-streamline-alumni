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

new #[Layout('layouts.app-admin')] class extends Component
{
    use WithFileUploads;

    public string $title = '';
    public string $description = '';
    public ?int $category_id = null;
    public string $status = 'draft';

    public $image = null;
    public array $attachments = [];

    public bool $showPreview = false;

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

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'min:3', 'max:255'],
            'description'   => ['nullable', 'string', 'max:5000'],
            'category_id'   => ['required', 'integer', Rule::exists('categories', 'id')],
            'status'        => ['required', Rule::in(['public', 'private', 'draft'])],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'attachments'   => ['nullable', 'array', 'max:5'],
            'attachments.*' => [
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,jpg,jpeg,png,webp,txt,csv',
            ],
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

    public function removeAttachment(int $index): void
    {
        $items = $this->attachments;
        unset($items[$index]);
        $this->attachments = array_values($items);
    }

    public function removeImage(): void
    {
        $this->image = null;
        $this->resetErrorBag('image');
    }

    // =========================================================
    //  SAVE
    // =========================================================

    public function save()
    {
        abort_unless(Auth::user()?->hasAnyRole(['registrar', 'program head']), 403);

        $validated = $this->validate();

        $imagePath       = null;
        $attachmentPaths = [];

        try {
            // --- 1. Store files first (outside transaction) ---
            if ($this->image) {
                $imagePath = $this->image->store('posts', 'public');
            }

            foreach ($this->attachments as $file) {
                if ($file) {
                    $attachmentPaths[] = $file->store('posts/attachments', 'public');
                }
            }

            // --- 2. Insert into DB inside a transaction ---
            // 👇 Now returns the created Post model
            $post = DB::transaction(function () use ($validated, $imagePath, $attachmentPaths) {
                return Post::create([
                    'user_id'     => Auth::id(),
                    'title'       => trim(strip_tags($validated['title'])),
                    'description' => filled($validated['description'])
                        ? trim(strip_tags($validated['description']))
                        : null,
                    'slug'        => $this->uniqueSlug($validated['title']),
                    'image'       => $imagePath,
                    'category_id' => $validated['category_id'],
                    'status'      => $validated['status'],
                    'attachments' => $attachmentPaths ?: null,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $this->cleanupFiles($imagePath, $attachmentPaths);

            if ($e->getCode() === '23000') {
                $this->addError('title', 'This title was just taken. Please try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not create post. Please try again.');
            return;
        } catch (\Throwable $e) {
            $this->cleanupFiles($imagePath, $attachmentPaths);

            report($e);
            session()->flash('error', 'Could not create post. Please try again.');
            return;
        }

        // --- 3. Email blast to alumni if the post is public ---
        // Runs AFTER the transaction commits. Queued — doesn't block the response.
        if ($validated['status'] === 'public') {
            SendPostAnnouncementEmail::dispatch($post->id)->afterCommit();
        }

        Cache::forget('posts:count');

        session()->flash('success', $validated['status'] === 'public'
            ? 'Post published. Alumni will be notified shortly.'
            : 'Post created successfully.');

        return redirect()->route('admin.post.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $i    = 2;

        while (Post::where('slug', $slug)->exists()) {
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
};