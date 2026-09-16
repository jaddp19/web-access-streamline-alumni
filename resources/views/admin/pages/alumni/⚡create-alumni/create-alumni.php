<?php

use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-admin')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $school_id = '';
    public ?int $batch_id = null;
    public ?int $course_id = null;

    protected function rules()
    {
        return [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email',
            'school_id' => 'required|string|max:255|unique:users,school_id',
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
    // It just follows whichever course is picked above.
    #[Computed]
    public function selectedDepartment()
    {
        if (! $this->course_id) {
            return null;
        }

        return Course::with('department')->find($this->course_id)?->department;
    }

    public function saveAlumni()
    {
        $validated = $this->validate();

        $validated['name']      = $this->sanitizeData($validated['name']);
        $validated['email']     = $this->sanitizeData($validated['email']);
        $validated['school_id'] = $this->sanitizeData($validated['school_id']);

        try {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'school_id' => $validated['school_id'],
                'password'  => Hash::make(Str::random(32)),
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('alumni');
            }

            $profile = UserProfile::create([
                'user_id'     => $user->id,
                'avatar'      => '',
                'location'    => [],
                'batch_id'    => $validated['batch_id'],
                'is_private'  => false,
                'is_verified' => false,
            ]);

            // Department is never stored directly — it's implied by course_id
            // via courses.department_id, so we only attach the course here.
            DB::table('student_course')->insert([
                'course_id'        => $validated['course_id'],
                'user_profile_id'  => $profile->id,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            session()->flash('success', 'Alumni account created. They can log in and complete the rest of their profile themselves.');
            return redirect()->route('admin.alumni.view');

        } catch (\Throwable $e) {
            logger()->error('Alumni creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            session()->flash('error', 'Something went wrong while creating this alumni: ' . $e->getMessage());
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
