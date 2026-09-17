<?php

use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\EmailTemplateService;
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
            DB::transaction(function () use ($validated) {
                $user = User::create([
                    'name'      => $validated['name'],
                    'email'     => $validated['email'],
                    'school_id' => $validated['school_id'],
                    'password'  => Hash::make('csav.alumni'),
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

                DB::table('student_course')->insert([
                    'course_id'        => $validated['course_id'],
                    'user_profile_id'  => $profile->id,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                // Send the notification email after the DB work succeeds.
                // If the template is missing, the send will throw and roll back
                // — so wrap in try/catch inside the transaction if you want
                // the account created regardless of email status.
                try {
                    $course = Course::with('department')->find($validated['course_id']);

                    EmailTemplateService::send(
                        'welcome-to-the-csav-alumni-network-name',
                        $validated['email'],
                        [
                            'name'           => $validated['name'],
                            'school_email'   => $validated['email'],
                            'school_id'      => $validated['school_id'],
                            'batch'          => Batch::find($validated['batch_id'])?->batch_name ?? '—',
                            'course'         => $course?->course_title ?? '—',
                            'department'     => $course?->department?->dept_name ?? '—',
                            'default_password' => 'csav.alumni',
                            'login_url'      => route('login'),
                        ]
                    );
                } catch (\Throwable $e) {
                    // Log but don't kill the whole operation — the account exists.
                    logger()->warning('Alumni welcome email failed: ' . $e->getMessage(), [
                        'email' => $validated['email'],
                    ]);
                }
            });

            session()->flash('success', 'Alumni account created. A welcome email was sent to ' . $validated['email'] . '.');
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