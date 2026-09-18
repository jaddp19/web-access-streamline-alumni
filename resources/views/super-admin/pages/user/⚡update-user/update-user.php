<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts::app-super-admin')] class extends Component
{
    public User $user;
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $school_id = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?string $selectedRole = null;

    /**
     * Composed full name — matches the auto-synced `users.name` value.
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

    protected function rules()
    {
        return [
            'first_name'  => 'required|string|min:2|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name'   => 'required|string|min:2|max:255',
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:users,email,' . $this->user->id,
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
            'password'     => 'nullable|string|min:6|confirmed',
            'selectedRole' => 'nullable|exists:roles,name',
            'school_id'    => 'required|string|max:9|unique:users,school_id,' . $this->user->id,
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
            'password.confirmed'    => 'Confirmation password does not match the password.',
            'email.unique'          => 'The email address is already registered.',
            'email.required'        => 'The email address is required.',
        ];
    }

    public function mount(User $user)
    {
        $this->user = $user;

        // Hydrate name parts. If parts are empty (legacy user), split from `name`.
        $first  = $user->first_name;
        $middle = $user->middle_name;
        $last   = $user->last_name;

        if (! $first && ! $last && $user->name) {
            $split = preg_split('/\s+/', trim($user->name));
            $first  = $split[0] ?? '';
            $last   = count($split) > 1 ? end($split) : '';
            $middle = count($split) > 2
                ? implode(' ', array_slice($split, 1, -1))
                : '';
        }

        $this->first_name  = $first ?? '';
        $this->middle_name = $middle ?? '';
        $this->last_name   = $last ?? '';

        $this->email       = $user->email ?? '';
        $this->school_id   = $user->school_id ?? '';
        $this->selectedRole = $user->roles->pluck('name')->first();
    }

    public function update()
    {
        $validated = $this->validate();

        $validated['first_name']  = $this->sanitizeData($validated['first_name']);
        $validated['middle_name'] = $validated['middle_name'] ? $this->sanitizeData($validated['middle_name']) : null;
        $validated['last_name']   = $this->sanitizeData($validated['last_name']);
        $validated['email']       = $this->sanitizeData($validated['email']);
        $validated['school_id']   = $this->sanitizeData($validated['school_id']);

        $this->user->update([
            'first_name'  => $validated['first_name'],
            'middle_name' => $validated['middle_name'],
            'last_name'   => $validated['last_name'],
            // 'name' auto-fills via the User model's saving hook
            'email'       => $validated['email'],
            'school_id'   => $validated['school_id'],
            'password'    => $validated['password']
                ? Hash::make($validated['password'])
                : $this->user->password,
        ]);

        // Guard against a cleared role selection before syncing
        if ($this->selectedRole) {
            $this->user->syncRoles($this->selectedRole);
        } else {
            $this->user->syncRoles([]);
        }

        session()->flash('success', 'User updated successfully.');
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