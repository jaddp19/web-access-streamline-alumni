<?php

namespace App\Livewire\Admin;

use App\Jobs\SendEventInvitationEmail;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-admin')] class extends Component
{
    use WithFileUploads;

    public string $title = '';
    public string $description = '';
    public string $location = '';
    public string $starts_at = '';
    public string $ends_at = '';
    public string $registration_deadline = '';
    public ?int $capacity = null;
    public string $status = 'draft';

    public $image = null;

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:3',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (trim($value) === '') {
                        $fail('The title cannot be empty or whitespace.');
                    }
                },
            ],

            'description' => ['nullable', 'string', 'max:5000'],

            'location' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '' && trim($value) === '') {
                        $fail('The location cannot be whitespace only.');
                    }
                },
            ],

            'starts_at' => [
                'required',
                'date',
                'after:now',
                'before:+5 years',
            ],

            'ends_at' => [
                'nullable',
                'date',
                'after:starts_at',
                function ($attribute, $value, $fail) {
                    if (! $value || ! $this->starts_at) return;

                    try {
                        $start = \Carbon\Carbon::parse($this->starts_at);
                        $end   = \Carbon\Carbon::parse($value);

                        if ($end->diffInDays($start) > 30) {
                            $fail('The end date cannot be more than 30 days after the start.');
                        }
                    } catch (\Throwable $e) {
                        // Base date rule handles malformed input
                    }
                },
            ],

            'registration_deadline' => [
                'nullable',
                'date',
                'before_or_equal:starts_at',
                function ($attribute, $value, $fail) {
                    if (! $value) return;

                    try {
                        $deadline = \Carbon\Carbon::parse($value);

                        if ($this->status === 'published' && $deadline->isPast()) {
                            $fail('The registration deadline cannot be in the past for a published event.');
                        }

                        if ($this->starts_at) {
                            $start = \Carbon\Carbon::parse($this->starts_at);

                            if ($deadline->diffInDays($start) > 365) {
                                $fail('The registration deadline is too far before the event start.');
                            }
                        }
                    } catch (\Throwable $e) {
                        // Base date rule handles malformed input
                    }
                },
            ],

            'capacity' => ['nullable', 'integer', 'min:1', 'max:10000'],

            'status' => ['required', 'in:draft,published'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required'                         => 'Please enter an event title.',
            'title.min'                              => 'Title must be at least 3 characters.',
            'title.max'                              => 'Title cannot exceed 255 characters.',
            'description.max'                        => 'Description cannot exceed 5000 characters.',
            'location.max'                           => 'Location cannot exceed 255 characters.',
            'starts_at.required'                     => 'Please pick a start date and time.',
            'starts_at.date'                         => 'The start date is not a valid date.',
            'starts_at.after'                        => 'Start date must be in the future.',
            'starts_at.before'                       => 'The start date cannot be more than 5 years in the future.',
            'ends_at.date'                           => 'The end date is not a valid date.',
            'ends_at.after'                          => 'The end date must be after the start date.',
            'registration_deadline.date'             => 'The registration deadline is not a valid date.',
            'registration_deadline.before_or_equal'  => 'The registration deadline cannot be after the event starts.',
            'capacity.integer'                       => 'Capacity must be a whole number.',
            'capacity.min'                           => 'Capacity must be at least 1.',
            'capacity.max'                           => 'Capacity cannot exceed 10,000.',
            'status.in'                              => 'The selected status is invalid.',
            'image.image'                            => 'Cover image must be a valid image file.',
            'image.mimes'                            => 'Cover image must be a JPG, PNG, or WebP file.',
            'image.max'                              => 'Cover image cannot exceed 5MB.',
        ];
    }

    // =========================================================
    //  FILE HANDLING
    // =========================================================

    public function updatedImage(): void
    {
        $this->validateOnly('image');
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

        $this->validate();

        // --- Cross-field guard ---
        if ($this->status === 'published'
            && \Carbon\Carbon::parse($this->starts_at)->isPast()
        ) {
            $this->addError('starts_at', 'You cannot publish an event with a start date in the past.');
            return;
        }

        $validated = $this->only([
            'title', 'description', 'location',
            'starts_at', 'ends_at', 'registration_deadline',
            'capacity', 'status',
        ]);

        $imagePath = null;

        try {
            if ($this->image) {
                $imagePath = $this->image->store('events', 'public');
            }

            $event = DB::transaction(function () use ($validated, $imagePath) {
                return Event::create([
                    'title'                  => trim(strip_tags($validated['title'])),
                    'slug'                   => $this->uniqueSlug($validated['title']),
                    'description'            => filled($validated['description']) ? trim(strip_tags($validated['description'])) : null,
                    'image'                  => $imagePath,
                    'location'               => filled($validated['location']) ? trim(strip_tags($validated['location'])) : null,
                    'starts_at'              => $validated['starts_at'],
                    'ends_at'                => $validated['ends_at'] ?: null,
                    'registration_deadline'  => $validated['registration_deadline'] ?: null,
                    'capacity'               => $validated['capacity'],
                    'status'                 => $validated['status'],
                    'created_by'             => Auth::id(),
                ]);
            });
        } catch (\Throwable $e) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            report($e);
            session()->flash('error', 'Could not create event. Please try again.');
            return;
        }

        // Fire invitation blast if published immediately
        if ($validated['status'] === 'published') {
            SendEventInvitationEmail::dispatch($event->id)->afterCommit();
        }

        session()->flash('success', $validated['status'] === 'published'
            ? 'Event published. Alumni will be notified shortly.'
            : 'Event saved as draft.');

        return redirect()->route('admin.events.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'event';
        $slug = $base;
        $i = 2;

        while (Event::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
};