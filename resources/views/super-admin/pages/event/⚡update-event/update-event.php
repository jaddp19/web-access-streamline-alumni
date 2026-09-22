<?php

use App\Jobs\SendEventInvitationEmail;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithFileUploads;

    public Event $event;

    public string $title = '';
    public string $description = '';
    public string $location = '';
    public string $starts_at = '';
    public string $ends_at = '';
    public string $registration_deadline = '';
    public ?int $capacity = null;
    public string $status = 'draft';

    public $image = null;
    public ?string $currentImage = null;

    public bool $resendInvitation = false;

    public function mount(Event $event): void
    {
        abort_unless(Auth::user()?->hasAnyRole(['super admin', 'registrar']), 403);

        $this->event = $event;

        $this->title                 = $event->title;
        $this->description           = $event->description ?? '';
        $this->location              = $event->location ?? '';
        $this->starts_at             = $event->starts_at?->format('Y-m-d\TH:i') ?? '';
        $this->ends_at               = $event->ends_at?->format('Y-m-d\TH:i') ?? '';
        $this->registration_deadline = $event->registration_deadline?->format('Y-m-d\TH:i') ?? '';
        $this->capacity              = $event->capacity;
        $this->status                = $event->status;
        $this->currentImage          = $event->image;
    }

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
                // Reject whitespace-only titles
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
                // Reject whitespace-only location if provided
                function ($attribute, $value, $fail) {
                    if ($value !== null && $value !== '' && trim($value) === '') {
                        $fail('The location cannot be whitespace only.');
                    }
                },
            ],

            // Must be a valid datetime, cannot be absurdly far out
            'starts_at' => [
                'required',
                'date',
                'before:+5 years',   // no event 5+ years in the future
            ],

            // Ends after start, within 30 days of start
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
                        // Let the base date rule handle malformed input
                    }
                },
            ],

            // Deadline must be before start, and cannot be in the past (when publishing)
            'registration_deadline' => [
                'nullable',
                'date',
                'before_or_equal:starts_at',
                function ($attribute, $value, $fail) {
                    if (! $value) return;

                    try {
                        $deadline = \Carbon\Carbon::parse($value);
                        $start    = $this->starts_at ? \Carbon\Carbon::parse($this->starts_at) : null;

                        // Deadline cannot be in the past if the event is being published
                        if ($this->status === 'published' && $deadline->isPast()) {
                            $fail('The registration deadline cannot be in the past for a published event.');
                        }

                        // Deadline cannot be more than 1 year before the start
                        if ($start && $deadline->diffInDays($start) > 365) {
                            $fail('The registration deadline is too far before the event start.');
                        }
                    } catch (\Throwable $e) {
                        // Let the base date rule handle malformed input
                    }
                },
            ],

            // Capacity must be >= current attendee count
            'capacity' => [
                'nullable',
                'integer',
                'min:1',
                'max:10000',
                function ($attribute, $value, $fail) {
                    if (! $value) return;

                    $currentAttendees = $this->event->attendeeCount();

                    if ($value < $currentAttendees) {
                        $fail("Capacity cannot be less than the {$currentAttendees} alumni who already RSVP'd.");
                    }
                },
            ],

            'status' => ['required', Rule::in(['draft', 'published', 'cancelled', 'completed'])],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'resendInvitation' => ['boolean'],
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
            'starts_at.before'                       => 'The start date cannot be more than 5 years in the future.',
            'ends_at.date'                           => 'The end date is not a valid date.',
            'ends_at.after'                          => 'The end date must be after the start date.',
            'registration_deadline.date'             => 'The deadline is not a valid date.',
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
    //  CROSS-FIELD VALIDATION (runs after rules pass)
    // =========================================================

    /**
     * Extra logic that needs to consider multiple fields together —
     * runs after individual rules pass.
     */
    public function save()
    {
        abort_unless(Auth::user()?->hasAnyRole(['super admin', 'registrar']), 403);

        $this->validate();

        // --- Cross-field guards ---

        // 1. Cannot publish an event whose start time is in the past
        if ($this->status === 'published' && \Carbon\Carbon::parse($this->starts_at)->isPast()) {
            $this->addError('starts_at', 'You cannot publish an event with a start date in the past.');
            return;
        }

        // 2. Cannot set status to "completed" for an event that hasn't ended yet
        if ($this->status === 'completed') {
            $endOrStart = $this->ends_at ?: $this->starts_at;

            if (\Carbon\Carbon::parse($endOrStart)->isFuture()) {
                $this->addError('status', 'You cannot mark an event as completed before it has ended.');
                return;
            }
        }

        // 3. Cannot cancel an event that already has attendees unless it's still upcoming
        if ($this->status === 'cancelled'
            && $this->event->status !== 'cancelled'
            && $this->event->attendeeCount() > 0
            && \Carbon\Carbon::parse($this->starts_at)->isPast()
        ) {
            $this->addError('status', 'You cannot cancel a past event with existing attendees.');
            return;
        }

        $validated = $this->only([
            'title', 'description', 'location',
            'starts_at', 'ends_at', 'registration_deadline',
            'capacity', 'status',
        ]);

        $wasPublished = $this->event->status === 'published';

        $newImagePath = null;
        $finalImagePath = $this->currentImage;

        try {
            if ($this->image) {
                $newImagePath = $this->image->store('events', 'public');
                $finalImagePath = $newImagePath;
            } elseif (! $this->currentImage) {
                $finalImagePath = null;
            }

            DB::transaction(function () use ($validated, $finalImagePath) {
                $this->event->update([
                    'title'                  => trim(strip_tags($validated['title'])),
                    'description'            => filled($validated['description']) ? trim(strip_tags($validated['description'])) : null,
                    'image'                  => $finalImagePath,
                    'location'               => filled($validated['location']) ? trim(strip_tags($validated['location'])) : null,
                    'starts_at'              => $validated['starts_at'],
                    'ends_at'                => $validated['ends_at'] ?: null,
                    'registration_deadline'  => $validated['registration_deadline'] ?: null,
                    'capacity'               => $validated['capacity'],
                    'status'                 => $validated['status'],
                ]);
            });
        } catch (\Throwable $e) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            report($e);
            session()->flash('error', 'Could not update event. Please try again.');
            return;
        }

        // Cleanup old image
        $originalImage = $this->event->getOriginal('image');
        if ($originalImage && $originalImage !== $finalImagePath) {
            Storage::disk('public')->delete($originalImage);
        }

        // Blast decision
        $justPublished = ! $wasPublished && $validated['status'] === 'published';
        $adminRequestedResend = $wasPublished
            && $validated['status'] === 'published'
            && $this->resendInvitation;

        $shouldBlast = $justPublished || $adminRequestedResend;

        if ($shouldBlast) {
            SendEventInvitationEmail::dispatch($this->event->id)->afterCommit();
        }

        $this->resendInvitation = false;

        session()->flash('success', match (true) {
            $justPublished        => 'Event published. Alumni will be notified shortly.',
            $adminRequestedResend => 'Event updated. Invitation re-sent to all alumni.',
            default               => 'Event updated successfully.',
        });

        return redirect()->route('super-admin.events.view');
    }

    public function updatedImage(): void { $this->validateOnly('image'); }

    public function removeImage(): void
    {
        $this->image = null;
        $this->resetErrorBag('image');
    }

    public function removeExistingImage(): void
    {
        $this->currentImage = null;
    }
};