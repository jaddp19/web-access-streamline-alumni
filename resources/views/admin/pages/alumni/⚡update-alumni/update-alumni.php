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

    public string $name = '';
    public string $email = '';
    public string $school_id = '';
    public ?int $batch_id = null;
    public ?int $course_id = null;

    public function mount(User $user): void
    {
        $this->user = $user;

        $this->name      = $user->name;
        $this->email     = $user->email;
        $this->school_id = $user->school_id ?? '';

        $profile = UserProfile::where('user_id', $user->id)->with('courses')->first();

        $this->batch_id  = $profile?->batch_id;
        $this->course_id = $profile?->courses->first()?->id;
    }

    protected function rules()
    {
        return [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'school_id' => 'required|string|max:255|unique:users,school_id,' . $this->user->id,
            'batch_id'  => 'required|exists:batches,id',
            'course_id' => 'required|exists:courses,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required'      => 'The name field is required.',
            'email.required'     => 'The email field is required.',
            'email.email'        => 'The email must be a valid email address.',
            'email.unique'       => 'This email is already registered.',
            'school_id.required' => 'The school ID field is required.',
            'school_id.unique'   => 'This school ID is already registered.',
            'batch_id.required'  => 'Please select a batch.',
            'batch_id.exists'    => 'The selected batch is invalid.',
            'course_id.required' => 'Please select a course.',
            'course_id.exists'   => 'The selected course is invalid.',
        ];
    }

    #[Computed]
    public function batches()
    {
        return Batch::orderBy('batch_name')->get();
    }

    #[Computed]
    public function courses()
    {
        return Course::with('department')
            ->where('is_active', true)
            ->orderBy('course_title')
            ->get();
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

        $validated['name']      = $this->sanitizeData($validated['name']);
        $validated['email']     = $this->sanitizeData($validated['email']);
        $validated['school_id'] = $this->sanitizeData($validated['school_id']);

        try {
            DB::transaction(function () use ($validated) {
                // 1. Update the user record
                $this->user->update([
                    'name'      => $validated['name'],
                    'email'     => $validated['email'],
                    'school_id' => $validated['school_id'],
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