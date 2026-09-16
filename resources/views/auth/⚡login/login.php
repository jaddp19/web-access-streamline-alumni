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
        if (! Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Wrong password.');
            return;
        }

        // Attempt login
        if (! Auth::attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->remember
        )) {
            $this->addError('auth', 'Login failed.');
            return $this->reset('email', 'password');
        }

        request()->session()->regenerate();

        $user = Auth::user();

        // IMPORTANT: this is a strict elseif chain — a user can only ever
        // match ONE of these branches. Registrar and program head NEVER
        // reach the form-check below; that logic lives only inside the
        // alumni branch, on purpose.
        if ($user->hasRole('registrar')) {
            return redirect()->route('super-admin.dashboard');

        } elseif ($user->hasRole('program head')) {
            return redirect()->route('admin.dashboard');

        } elseif ($user->hasRole('alumni')) {
            // Only alumni ever get routed to the onboarding form,
            // and only if they haven't submitted a tracer study yet.
            if (! \App\Models\TracerStudy::where('user_id', $user->id)->exists()) {
                return redirect()->route('form');
            }
            return redirect()->route('alumni.dashboard');

        } else {
            // No recognized role at all — log them out immediately
            // rather than leaving them authenticated with nowhere valid to go.
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            $this->addError('email', 'This account has no assigned role. Please contact the registrar.');
            return;
        }
    }
};
