<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
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
        $user = \App\Models\User::where('email', $this->email)->first();

        if (! $user) {
            $this->addError('email', 'This email is not registered.');
            return;
        }

        // Now check password
        if (! \Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Wrong password.');
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

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('super-admin')) {
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

