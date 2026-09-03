<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $school_id = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('The email address is invalid.');
                        return;
                    }

                    $domain = substr(strrchr($value, "@"), 1);

                    if (!checkdnsrr($domain, "MX")) {
                        $fail('The email domain is not valid.');
                        return;
                    }
                },
            ],
            'school_id' => 'required|string|max:9|unique:users,school_id',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name is required.',
            'name.min' => 'The name must be at least 3 characters.',
            'email.required' => 'The email address is required.',
            'email.unique' => 'The email address is already registered.',
            'school_id.required' => 'Your school ID number is required.',
            'school_id.unique' => 'This school ID is already registered to an account.',
            'school_id.max' => 'Your school ID number must not exceed 9 characters.',
            'password.required' => 'The password is required.',
            'password.confirmed' => 'Confirmation password does not match the password.',
        ];
    }

    public function register()
    {
        $validated = $this->validate();

        $validated['name'] = $this->sanitizeData($validated['name']);
        $validated['email'] = $this->sanitizeData($validated['email']);
        $validated['school_id'] = $this->sanitizeData($validated['school_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'school_id' => $validated['school_id'],
            'password' => Hash::make($validated['password']),
            'verification_status' => 'pending',
        ]);

        $user->assignRole('pending-verification');

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->route('verification.pending')
            ->with('status', 'Thanks for registering! Your account is under review — we\'ll notify you once approved.');
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }
};
