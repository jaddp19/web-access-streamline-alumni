<?php

use App\Models\Batch;
use App\Models\Course;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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

    public function mount(User $user): void
    {
        $this->user = $user->load([
            'userProfile:id,user_id,batch_id',
            'userProfile.courses:id,course_title',
        ]);

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

        $this->email     = $user->email ?? '';
        $this->school_id = $user->school_id ?? '';

        $profile = $user->userProfile;

        $this->batch_id  = $profile?->batch_id;
        $this->course_id = $profile?->courses->first()?->id;
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function fullName(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    /** Cached array of ['id', 'batch_name'] — safe across cache boundary. */
    #[Computed]
    public function batches(): array
    {
        return Cache::remember('admin:batch-list', now()->addMinutes(10), function () {
            return Batch::query()
                ->orderByDesc('batch_name')
                ->get(['id', 'batch_name'])
                ->map(fn ($b) => ['id' => (int) $b->id, 'name' => (string) $b->batch_name])
                ->all();
        });
    }

    /** Cached array of ['id', 'course_title', 'department_id']. */
    #[Computed]
    public function courses(): array
    {
        return Cache::remember('admin:course-list', now()->addMinutes(10), function () {
            return Course::query()
                ->where('is_active', true)
                ->orderBy('course_title')
                ->get(['id', 'course_title', 'department_id'])
                ->map(fn ($c) => [
                    'id'            => (int) $c->id,
                    'title'         => (string) $c->course_title,
                    'department_id' => (int) $c->department_id,
                ])
                ->all();
        });
    }

    /** Department name for the selected course — no extra DB hit. */
    #[Computed]
    public function selectedDepartmentName(): ?string
    {
        if (! $this->course_id) {
            return null;
        }

        $deptId = collect($this->courses)
            ->firstWhere('id', $this->course_id)['department_id'] ?? null;

        if (! $deptId) {
            return null;
        }

        return \App\Models\Department::query()->whereKey($deptId)->value('dept_name');
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'first_name'  => ['required', 'string', 'min:2', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'min:2', 'max:255'],
            'email'       => [
                'required', 'email:rfc,dns', 'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],
            'school_id'   => [
                'required', 'string', 'max:20',
                Rule::unique('users', 'school_id')->ignore($this->user->id),
            ],
            'batch_id'    => ['required', 'integer', Rule::exists('batches', 'id')],
            'course_id'   => ['required', 'integer', Rule::exists('courses', 'id')],
        ];
    }

    public function messages(): array
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
            'school_id.max'       => 'The school ID may not be greater than 20 characters.',
            'batch_id.required'   => 'Please select a batch.',
            'batch_id.exists'     => 'The selected batch is invalid.',
            'course_id.required'  => 'Please select a course.',
            'course_id.exists'    => 'The selected course is invalid.',
        ];
    }

    // =========================================================
    //  UPDATE
    // =========================================================

    public function updateAlumni()
    {
        abort_unless(Auth::user()?->hasAnyRole(['registrar', 'program head']), 403);

        $validated = $this->validate();

        $firstName  = $this->sanitize($validated['first_name']);
        $middleName = $validated['middle_name'] ? $this->sanitize($validated['middle_name']) : null;
        $lastName   = $this->sanitize($validated['last_name']);
        $email      = $this->sanitize($validated['email']);
        $schoolId   = $this->sanitize($validated['school_id']);

        try {
            DB::transaction(function () use (
                $firstName, $middleName, $lastName, $email, $schoolId, $validated
            ) {
                // 1. Update the user record.
                $this->user->update([
                    'first_name'  => $firstName,
                    'middle_name' => $middleName,
                    'last_name'   => $lastName,
                    'email'       => $email,
                    'school_id'   => $schoolId,
                ]);

                // 2. Get or create the profile.
                $profile = UserProfile::firstOrCreate(
                    ['user_id' => $this->user->id],
                    [
                        'avatar'      => null,
                        'location'    => [],
                        'is_private'  => false,
                        'is_verified' => false,
                        'batch_id'    => $validated['batch_id'],
                    ]
                );

                if (! $profile->wasRecentlyCreated) {
                    $profile->update(['batch_id' => $validated['batch_id']]);
                }

                // 3. Sync the course pivot — one course per alumni.
                $profile->courses()->sync([$validated['course_id']]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                $field = str_contains($e->getMessage(), 'school_id') ? 'school_id' : 'email';
                $this->addError($field, 'This value was just taken by another account. Please refresh and try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not update the alumni account. Please try again.');
            return;
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Could not update the alumni account. Please try again.');
            return;
        }

        session()->flash('success', 'Alumni details updated successfully.');

        return redirect()->route('admin.alumni.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    protected function sanitize(mixed $data): mixed
    {
        return is_string($data) ? trim(strip_tags($data)) : $data;
    }
};