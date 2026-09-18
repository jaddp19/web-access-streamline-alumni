<?php

use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-admin')] class extends Component
{
    public User $user;

    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $school_id = '';
    public ?int $batch_id = null;
    public ?int $course_id = null;

    /**
     * Composed full name — matches the auto-synced `users.name`.
     */
    #[Computed]
    public function fullName(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    public function mount(User $user): void
    {
        $this->user = $user;

        // Hydrate name parts. If empty (legacy user), split from `name`.
        $first  = $user->first_name;
        $middle = $user->middle_name;
        $last   = $user->last_name;

        if (! $first && ! $last && $user->name) {
            $split  = preg_split('/\s+/', trim($user->name));
            $first  = $split[0] ?? '';
            $last   = count($split) > 1 ? end($split) : '';
            $middle = count($split) > 2
                ? implode(' ', array_slice($split, 1, -1))
                : '';
        }

        $this->first_name  = $first ?? '';
        $this->middle_name = $middle ?? '';
        $this->last_name   = $last ?? '';

        $this->email     = $user->email;
        $this->school_id = $user->school_id ?? '';

        $profile = UserProfile::where('user_id', $user->id)->with('courses')->first();

        $this->batch_id  = $profile?->batch_id;
        $this->course_id = $profile?->courses->first()?->id;
    }

    protected function rules()
    {
        return [
            'first_name'  => 'required|string|min:2|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|min:2|max:255',
            'email'       => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'school_id'   => 'required|string|max:255|unique:users,school_id,' . $this->user->id,
            'batch_id'    => 'required|exists:batches,id',
            'course_id'   => 'required|exists:courses,id',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'The first name is required.',
            'first_name.min'      => 'The first name must be at least 2 characters.',
            'first_name.max'      => 'The first name may not be greater than 255 characters.',
            'last_name.required'  => 'The last name is required.',
            'last_name.min'       => 'The last name must be at least 2 characters.',
            'last_name.max'       => 'The last name may not be greater than 255 characters.',
            'email.required'      => 'The email field is required.',
            'email.email'         => 'The email must be a valid email address.',
            'email.unique'        => 'This email is already registered.',
            'school_id.required'  => 'The school ID field is required.',
            'school_id.unique'    => 'This school ID is already registered.',
            'batch_id.required'   => 'Please select a batch.',
            'batch_id.exists'     => 'The selected batch is invalid.',
            'course_id.required'  => 'Please select a course.',
            'course_id.exists'    => 'The selected course is invalid.',
        ];
    }


    // Auto-derived — the admin never selects this directly.
    #[Computed]
    public function selectedDepartment()
    {
        if (! $this->course_id) {
            return null;
        }

        return Course::with('department')->find($this->course_id)?->department;
    }

    public function updateAlumni()
    {
        $validated = $this->validate();

        $validated['first_name']  = $this->sanitizeData($validated['first_name']);
        $validated['middle_name'] = $validated['middle_name'] ? $this->sanitizeData($validated['middle_name']) : null;
        $validated['last_name']   = $this->sanitizeData($validated['last_name']);
        $validated['email']       = $this->sanitizeData($validated['email']);
        $validated['school_id']   = $this->sanitizeData($validated['school_id']);

        try {
            DB::transaction(function () use ($validated) {
                // 1. Update the user record
                $this->user->update([
                    'first_name'  => $validated['first_name'],
                    'middle_name' => $validated['middle_name'],
                    'last_name'   => $validated['last_name'],
                    // 'name' auto-fills via the User model's saving hook
                    'email'       => $validated['email'],
                    'school_id'   => $validated['school_id'],
                ]);

                // 2. Update the profile (create it if it somehow doesn't exist)
                $profile = UserProfile::firstOrCreate(
                    ['user_id' => $this->user->id],
                    [
                        'avatar'      => '',
                        'location'    => [],
                        'is_private'  => false,
                        'is_verified' => false,
                    ]
                );

                $profile->update([
                    'batch_id' => $validated['batch_id'],
                ]);

                // 3. Sync the course pivot — one course per alumni in this system
                $profile->courses()->sync([$validated['course_id']]);
            });

            session()->flash('success', 'Alumni details updated successfully.');
            return redirect()->route('admin.alumni.view');

        } catch (\Throwable $e) {
            logger()->error('Alumni update failed: ' . $e->getMessage(), [
                'user_id' => $this->user->id,
                'trace'   => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Something went wrong while updating this alumni: ' . $e->getMessage());
            return;
        }
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }
};