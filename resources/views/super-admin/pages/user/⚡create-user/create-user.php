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
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $selectedRole = '';


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
            'password' => 'required|string|min:6|confirmed',
            'selectedRole' => 'exists:roles,name',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name is required.',
            'name.string' => 'The name must be a string.',
            'name.min' => 'The name must be at least 3 characters.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'name.unique' => 'The name is already taken.',
            'password.required' => 'The password is required.',
            'password.confirmed' => 'Confirmation password does not match the password.',
            'email.unique' => 'The email address is already registered.',
            'email.required' => 'The email address is required.',
        ];
    }


    public function create()
    {
        $validated = $this->validate();

        $validated['name'] = $this->sanitizeData($validated['name']);
        $validated['email'] = $this->sanitizeData($validated['email']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles($validated['selectedRole']);

        session()->flash('success', 'User created successfully.');
        return redirect()->route('admin.user.view');
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