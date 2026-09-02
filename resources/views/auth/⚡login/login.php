<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ];

    public function messages()
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
        ];
    }

    public function login()
    {
        $this->validate();

        // Check if user exists first
        $user = User::where('email', $this->email)->first();

        if (! $user) {
            $this->addError('email', 'This email is not registered.');
            return;
        }

        // Now check password
        if (!Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Wrong password.');
            return;
        }

        // Block pending accounts before they're allowed to authenticate.
        // Verification is decided solely by the registrar (super-admin);
        // login itself should not route them anywhere else.
        if ($user->hasRole('pending-verification')) {
            $this->addError('email', 'Your account is still pending registrar approval. Please wait for verification before logging in.');
            return;
        }

        // Attempt login
        if (Auth::attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->remember
        )) {
            request()->session()->regenerate();

            $user = Auth::user();

            if ($user->hasRole('alumni')) {
                return redirect()->route('alumni.dashboard');
            }

            if ($user->hasRole('program head')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('registrar')) {
                return redirect()->route('super-admin.dashboard');
            }

            if ($user->roles->isEmpty()) {
                abort(403, 'Unauthorized: Not Found.');
            }

            return redirect()->route('login');
        }

        $this->addError('auth', 'Login failed.');

        return $this->reset('email', 'password');
    }
};
