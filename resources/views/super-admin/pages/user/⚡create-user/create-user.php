<?php

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
    public string $school_id = '';
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

            'school_id' => [
                'required',
                'string',
                'max:9',
                Rule::unique('users', 'school_id'),
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
            'first_name.required'   => 'The first name is required.',
            'first_name.min'        => 'The first name must be at least 2 characters.',
            'first_name.max'        => 'The first name may not be greater than 255 characters.',
            'last_name.required'    => 'The last name is required.',
            'last_name.min'         => 'The last name must be at least 2 characters.',
            'last_name.max'         => 'The last name may not be greater than 255 characters.',
            'school_id.required'    => 'Your school ID number is required.',
            'school_id.unique'      => 'This school ID is already registered to an account.',
            'school_id.max'         => 'Your school ID number must not exceed 9 characters.',
            'email.unique'          => 'The email address is already registered.',
            'email.required'        => 'The email address is required.',
            'email.email'           => 'The email address is invalid.',
            'selectedRole.required' => 'Please select a role.',
            'selectedRole.in'       => 'The selected role is invalid.',
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
        $schoolId   = $this->sanitize($validated['school_id']);

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
                    str_contains($e->getMessage(), 'school_id') ? 'school_id' : 'email',
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