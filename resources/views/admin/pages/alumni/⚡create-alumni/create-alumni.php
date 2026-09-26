<?php

use App\Models\Batch;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-admin')] class extends Component
{
    private const DEFAULT_PASSWORD = 'csav.alumni';

    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $school_year = '';         // dropdown (from batches.batch_name)
    public string $school_id_suffix = '';    // 4-digit input

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
        return \Illuminate\Support\Facades\Cache::remember('batches:years-list', now()->addMinutes(10), function () {
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
            'email'       => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],

            'school_year' => [
                'required',
                Rule::in($this->batchYears),
                function ($attribute, $value, $fail) {
                    // Only validate uniqueness once the suffix is fully typed.
                    if (strlen($this->school_id_suffix) !== 4) {
                        return;
                    }
                    $combined = $value.'-'.$this->school_id_suffix;
                    if (User::where('school_id', $combined)->exists()) {
                        $fail('This school ID is already registered.');
                    }
                },
            ],

            'school_id_suffix' => [
                'required',
                'string',
                'digits:4',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'       => 'The first name is required.',
            'first_name.min'            => 'The first name must be at least 2 characters.',
            'first_name.max'            => 'The first name may not be greater than 255 characters.',
            'last_name.required'        => 'The last name is required.',
            'last_name.min'             => 'The last name must be at least 2 characters.',
            'last_name.max'             => 'The last name may not be greater than 255 characters.',
            'email.required'            => 'The email field is required.',
            'email.email'               => 'The email must be a valid email address.',
            'email.unique'              => 'This email is already registered.',
            'school_year.required'      => 'Please select a batch year.',
            'school_year.in'            => 'The selected batch year is invalid.',
            'school_id_suffix.required' => 'Please enter the last 4 digits of the school ID.',
            'school_id_suffix.digits'   => 'The last 4 digits must be numeric.',
        ];
    }

    // =========================================================
    //  SAVE
    // =========================================================

    public function saveAlumni()
    {
        abort_unless(Auth::user()?->hasAnyRole(['registrar', 'program head']), 403);

        $validated = $this->validate();

        $firstName  = $this->sanitize($validated['first_name']);
        $middleName = $validated['middle_name'] ? $this->sanitize($validated['middle_name']) : null;
        $lastName   = $this->sanitize($validated['last_name']);
        $email      = $this->sanitize($validated['email']);
        $schoolId   = $this->schoolId;   // composed from school_year + school_id_suffix
        $fullName   = trim(implode(' ', array_filter([$firstName, $middleName, $lastName])));

        try {
            DB::transaction(function () use ($firstName, $middleName, $lastName, $email, $schoolId) {
                $user = User::create([
                    'first_name'  => $firstName,
                    'middle_name' => $middleName,
                    'last_name'   => $lastName,
                    'email'       => $email,
                    'school_id'   => $schoolId,
                    'password'    => Hash::make(self::DEFAULT_PASSWORD),
                ]);

                $user->syncRoles(['alumni']);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                $field = str_contains($e->getMessage(), 'school_id') ? 'school_id_suffix' : 'email';
                $this->addError($field, 'This value was just registered. Please refresh and try again.');
                return;
            }

            report($e);
            session()->flash('error', 'Could not create the alumni account. Please try again.');
            return;
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Could not create the alumni account. Please try again.');
            return;
        }

        // Send welcome email — after DB commit so mail failure won't roll back.
        try {
            EmailTemplateService::send(
                'welcome-to-the-csav-alumni-network-name',
                $email,
                [
                    'name'          => $fullName,
                    'school_email'  => $email,
                    'school_id'     => $schoolId,
                    'temp_password' => self::DEFAULT_PASSWORD,
                    'login_url'     => route('login'),
                ]
            );

            session()->flash(
                'success',
                "Alumni account created. A welcome email was sent to {$email}."
            );
        } catch (\Throwable $e) {
            Log::warning('Alumni welcome email failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            session()->flash(
                'warning',
                "Alumni account created, but the welcome email could not be sent. Notify {$email} manually with the temp password: " . self::DEFAULT_PASSWORD
            );
        }

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