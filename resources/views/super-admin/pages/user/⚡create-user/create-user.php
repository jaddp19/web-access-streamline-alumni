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
    public string $name = '';
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
            'name' => 'required|string|min:3|max:255|unique:users,name',
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
            'name.required'         => 'The name is required.',
            'name.min'              => 'The name must be at least 3 characters.',
            'name.max'              => 'The name may not be greater than 255 characters.',
            'name.unique'           => 'The name is already taken.',
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

        $validated['name']      = $this->sanitizeData($validated['name']);
        $validated['email']     = $this->sanitizeData($validated['email']);
        $validated['school_id'] = $this->sanitizeData($validated['school_id']);

        $plainPassword = $this->generatedPassword;
        $templateSlug  = $this->templateSlugForRole($validated['selectedRole']);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'school_id' => $validated['school_id'],
            'password'  => Hash::make($plainPassword),
        ]);

        $user->syncRoles($validated['selectedRole']);

        EmailTemplateService::send(
            $templateSlug,
            $validated['email'],
            [
                'name'          => $validated['name'],
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