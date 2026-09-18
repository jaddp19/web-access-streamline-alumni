<?php

use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts::app-super-admin')] class extends Component
{
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $school_id = '';
    public string $selectedRole = '';

    /**
     * Password is auto-derived from the selected role.
     */
    #[Computed]
    public function generatedPassword(): ?string
    {
        if (blank($this->selectedRole)) {
            return null;
        }

        $slug = Str::of($this->selectedRole)
            ->lower()
            ->replace(' ', '-')
            ->toString();

        return 'csav.' . $slug;
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
     * Map role name → email template slug.
     */
    protected function templateSlugForRole(string $role): string
    {
        return match (Str::lower($role)) {
            'alumni'         => 'welcome-to-the-csav-alumni-network-name',
            'program head'   => 'welcome-program-head',
            'registrar'      => 'welcome-registrar',
            default          => 'your-csav-alumni-network-staff-account-has-been-created',
        };
    }

    protected function rules()
    {
        return [
            'first_name'  => 'required|string|min:2|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|min:2|max:255',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('The email address is invalid.');
                    }
                    $domain = substr(strrchr($value, "@"), 1);
                    if (!checkdnsrr($domain, "MX")) {
                        $fail('The email is not valid.');
                    }
                },
            ],
            'school_id'    => 'required|string|max:9|unique:users,school_id',
            'selectedRole' => 'required|exists:roles,name',
        ];
    }

    public function messages()
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
            'selectedRole.required' => 'Please select a role.',
            'selectedRole.exists'   => 'The selected role is invalid.',
        ];
    }

    public function create()
    {
        $validated = $this->validate();

        $validated['first_name']  = $this->sanitizeData($validated['first_name']);
        $validated['middle_name'] = $validated['middle_name'] ? $this->sanitizeData($validated['middle_name']) : null;
        $validated['last_name']   = $this->sanitizeData($validated['last_name']);
        $validated['email']       = $this->sanitizeData($validated['email']);
        $validated['school_id']   = $this->sanitizeData($validated['school_id']);

        $plainPassword = $this->generatedPassword;
        $templateSlug  = $this->templateSlugForRole($validated['selectedRole']);
        $fullName      = $this->fullName;

        $user = User::create([
            'first_name'  => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name'   => $validated['last_name'],
            // 'name' auto-fills via the User model's saving hook
            'email'       => $validated['email'],
            'school_id'   => $validated['school_id'],
            'password'    => Hash::make($plainPassword),
        ]);

        $user->syncRoles($validated['selectedRole']);

        EmailTemplateService::send(
            $templateSlug,
            $validated['email'],
            [
                'name'          => $fullName,
                'school_email'  => $validated['email'],
                'login_url'     => route('login'),
                'temp_password' => $plainPassword,
                'role'          => Str::headline($validated['selectedRole']),
            ]
        );

        session()->flash('success', "User created. Temporary password: {$plainPassword}");
        return redirect()->route('super-admin.user.view');
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }

    #[Computed()]
    public function roles()
    {
        return Role::select('id', 'name')->get();
    }
};