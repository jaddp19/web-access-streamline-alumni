<?php

use App\Models\Batch;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $school_year = '';         // dropdown (from batches.batch_name)
    public string $school_id_suffix = '';    // 4-digit input
    public string $selectedRole = '';

    // =========================================================
    //  COMPUTED
    // =========================================================

    /**
     * Password is auto-derived from the selected role.
     */
    #[Computed]
    public function generatedPassword(): ?string
    {
        if (blank($this->selectedRole)) {
            return null;
        }

        return 'csav.' . Str::of($this->selectedRole)
            ->lower()
            ->replace(' ', '-')
            ->toString();
    }

    /**
     * Composed full name — matches what will be saved in `users.name`.
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

    /**
     * Composed school ID: "YYYY-NNNN".
     */
    #[Computed]
    public function schoolId(): string
    {
        if (blank($this->school_year) || blank($this->school_id_suffix)) {
            return '';
        }

        return $this->school_year . '-' . $this->school_id_suffix;
    }

    /**
     * Roles are static — cache for 10 minutes to skip the query
     * on every Livewire render (keystroke, blur, etc.).
     */
    #[Computed(persist: true)]
    public function roles()
    {
        return Cache::remember('roles:name-list', now()->addMinutes(10), function () {
            return Role::query()
                ->orderBy('name')
                ->pluck('name')
                ->all();
        });
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
            'first_name'  => 'required|string|min:2|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|min:2|max:255',

            'email' => [
                'required',
                'email:rfc,dns',   // ✅ built-in DNS check — cached by Laravel validator
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'school_year' => [
                'required',
                Rule::in($this->batchYears),
                function ($attribute, $value, $fail) {
                    // Only validate uniqueness once the suffix is fully typed.
                    if (strlen($this->school_id_suffix) !== 4) {
                        return;
                    }
                    $combined = $value . '-' . $this->school_id_suffix;
                    if (User::where('school_id', $combined)->exists()) {
                        $fail('This school ID is already registered to an account.');
                    }
                },
            ],

            'school_id_suffix' => [
                'required',
                'string',
                'digits:4',
            ],

            'selectedRole' => [
                'required',
                Rule::in($this->roles),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'         => 'The first name is required.',
            'first_name.min'              => 'The first name must be at least 2 characters.',
            'first_name.max'              => 'The first name may not be greater than 255 characters.',
            'last_name.required'          => 'The last name is required.',
            'last_name.min'               => 'The last name must be at least 2 characters.',
            'last_name.max'               => 'The last name may not be greater than 255 characters.',
            'school_year.required'        => 'Please select a batch year.',
            'school_year.in'              => 'The selected batch year is invalid.',
            'school_id_suffix.required'   => 'Please enter the last 4 digits of the school ID.',
            'school_id_suffix.digits'     => 'The last 4 digits must be numeric.',
            'email.unique'                => 'The email address is already registered.',
            'email.required'              => 'The email address is required.',
            'email.email'                 => 'The email address is invalid.',
            'selectedRole.required'       => 'Please select a role.',
            'selectedRole.in'             => 'The selected role is invalid.',
        ];
    }

    // =========================================================
    //  CREATE
    // =========================================================

    public function create()
    {
        $validated = $this->validate();

        // Sanitize once, into locals — no repeated Str::of allocations.
        $firstName  = $this->sanitize($validated['first_name']);
        $middleName = $validated['middle_name'] ? $this->sanitize($validated['middle_name']) : null;
        $lastName   = $this->sanitize($validated['last_name']);
        $email      = $this->sanitize($validated['email']);
        $schoolId   = $this->schoolId;   // composed from school_year + school_id_suffix

        $plainPassword = $this->generatedPassword;
        $role          = $validated['selectedRole'];
        $fullName      = trim(implode(' ', array_filter([$firstName, $middleName, $lastName])));

        try {
            $user = DB::transaction(function () use (
                $firstName, $middleName, $lastName, $email, $schoolId, $plainPassword, $role
            ) {
                $user = User::create([
                    'first_name'  => $firstName,
                    'middle_name' => $middleName,
                    'last_name'   => $lastName,
                    // 'name' auto-fills via User model's saving hook
                    'email'       => $email,
                    'school_id'   => $schoolId,
                    'password'    => Hash::make($plainPassword),
                ]);

                $user->syncRoles($role);

                return $user;
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Race condition — someone inserted the same email/school_id
            // between validation and this insert. Show a friendly error.
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                $this->addError(
                    str_contains($e->getMessage(), 'school_id') ? 'school_id_suffix' : 'email',
                    'This value was just registered by another account. Please refresh and try again.'
                );
                return;
            }

            throw $e;
        }

        // Fire the email AFTER the DB commit (transaction callback returns),
        // and don't fail the request if SMTP is slow/down.
        $this->sendWelcomeEmail($user, $role, $fullName, $email, $plainPassword);

        session()->flash('success', "User created successfully. Temporary password: {$plainPassword}");
        session()->flash('generated_password', $plainPassword);

        return redirect()->route('super-admin.user.view');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    /**
     * Map role name → email template slug.
     */
    protected function templateSlugForRole(string $role): string
    {
        return match (Str::lower($role)) {
            'alumni'       => 'welcome-to-the-csav-alumni-network-name',
            'program head' => 'welcome-program-head',
            'registrar'    => 'welcome-registrar',
            default        => 'your-csav-alumni-network-staff-account-has-been-created',
        };
    }

    protected function sendWelcomeEmail(
        User $user,
        string $role,
        string $fullName,
        string $email,
        string $plainPassword
    ): void {
        try {
            EmailTemplateService::send(
                $this->templateSlugForRole($role),
                $email,
                [
                    'name'          => $fullName,
                    'school_email'  => $email,
                    'login_url'     => route('login'),
                    'temp_password' => $plainPassword,
                    'role'          => Str::headline($role),
                ]
            );
        } catch (\Throwable $e) {
            // User is already created — don't 500 the page. Log and
            // surface a non-blocking warning.
            Log::error('Welcome email failed', [
                'user_id' => $user->id,
                'role'    => $role,
                'error'   => $e->getMessage(),
            ]);

            session()->flash(
                'warning',
                "User was created, but the welcome email could not be sent. Please notify them manually."
            );
        }
    }

    protected function sanitize(mixed $data): mixed
    {
        return is_string($data)
            ? trim(strip_tags($data))
            : $data;
    }
};