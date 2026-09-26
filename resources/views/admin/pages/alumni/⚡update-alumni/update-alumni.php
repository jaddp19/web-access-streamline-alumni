<?php

use App\Models\Batch;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
    public string $school_year = '';         // dropdown (from batches.batch_name)
    public string $school_id_suffix = '';    // 4-digit input

    public function mount(User $user): void
    {
        $this->user = $user->load([
            'userProfile:id,user_id,batch_id',
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

        $this->email = $user->email ?? '';

        // Split the stored "YYYY-NNNN" back into year + suffix.
        $this->school_year      = '';
        $this->school_id_suffix = '';

        if ($user->school_id && str_contains($user->school_id, '-')) {
            [$year, $suffix] = explode('-', $user->school_id, 2);
            $this->school_year      = $year;
            $this->school_id_suffix = $suffix;
        }

        $this->batch_id = $user->userProfile?->batch_id;
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

    /**
     * Composed school ID: "YYYY-NNNN".
     */
    #[Computed]
    public function schoolId(): string
    {
        if (blank($this->school_year) || blank($this->school_id_suffix)) {
            return '';
        }

        return $this->school_year.'-'.$this->school_id_suffix;
    }

    /**
     * Batch years for the school-ID dropdown.
     */
    #[Computed(persist: true)]
    public function batchYears()
    {
        return Cache::remember('batches:years-list', now()->addMinutes(10), function () {
            return Batch::query()
                ->orderByDesc('batch_name')
                ->pluck('batch_name')
                ->map(fn ($y) => (string) $y)
                ->all();
        });
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

            'email' => [
                'required', 'email:rfc,dns', 'max:255',
                Rule::unique('users', 'email')->ignore($this->user->id),
            ],

            'school_year' => [
                'required',
                Rule::in($this->batchYears),
                function ($attribute, $value, $fail) {
                    // Only validate uniqueness once the suffix is fully typed.
                    if (strlen($this->school_id_suffix) !== 4) {
                        return;
                    }
                    $combined = $value.'-'.$this->school_id_suffix;
                    if (User::where('school_id', $combined)
                        ->where('id', '!=', $this->user->id)
                        ->exists()) {
                        $fail('This school ID is already registered.');
                    }
                },
            ],

            'school_id_suffix' => ['required', 'string', 'digits:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'        => 'The first name is required.',
            'first_name.min'             => 'The first name must be at least 2 characters.',
            'first_name.max'             => 'The first name may not be greater than 255 characters.',
            'last_name.required'         => 'The last name is required.',
            'last_name.min'              => 'The last name must be at least 2 characters.',
            'last_name.max'              => 'The last name may not be greater than 255 characters.',
            'email.required'             => 'The email field is required.',
            'email.email'                => 'The email must be a valid email address.',
            'email.unique'               => 'This email is already registered.',
            'school_year.required'       => 'Please select a batch year.',
            'school_year.in'             => 'The selected batch year is invalid.',
            'school_id_suffix.required'  => 'Please enter the last 4 digits of the school ID.',
            'school_id_suffix.digits'    => 'The last 4 digits must be numeric.',
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
        $schoolId   = $this->schoolId;   // composed from school_year + school_id_suffix

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
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                $field = str_contains($e->getMessage(), 'school_id') ? 'school_id_suffix' : 'email';
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