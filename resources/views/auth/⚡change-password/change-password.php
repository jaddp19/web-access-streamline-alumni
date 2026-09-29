<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;

new #[Layout('layouts.auth')] class extends Component
{
    public string $password = '';
    public string $password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();

        if (! $user || ! $user->needsForcedPasswordChange()) {
            return $this->redirectBasedOnRole();
        }
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                'min:8',
                'max:72',
                'confirmed',

                Password::min(8)
                    ->letters()
                    ->symbols()
                    ->uncompromised(),

                function ($attribute, $value, $fail) {
                    if (Hash::check($value, Auth::user()->password)) {
                        $fail('Your new password must be different from your current password.');
                    }
                },
            ],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'password.required'      => 'Please choose a new password.',
            'password.min'           => 'Your password must be at least 8 characters.',
            'password.max'           => 'Your password may not be longer than 72 characters.',
            'password.confirmed'     => 'The password confirmation does not match.',
            'password.letters'       => 'Your password must contain at least one letter.',
            'password.symbols'       => 'Your password must contain at least one symbol (e.g. ! @ # $ %).',
            'password.uncompromised' => 'This password has appeared in a data breach. Please choose a different one.',

            'password_confirmation.required' => 'Please confirm your new password.',
        ];
    }

    // =========================================================
    //  SAVE
    // =========================================================

    public function save()
    {
        $this->password              = trim($this->password);
        $this->password_confirmation = trim($this->password_confirmation);

        $validated = $this->validate();

        $user = Auth::user();

        $user->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        session()->flash('password_changed', 'Your password has been updated. Welcome aboard!');

        return $this->redirectBasedOnRole();
    }

    // =========================================================
    //  REDIRECT
    // =========================================================

    protected function redirectBasedOnRole()
    {
        $user  = Auth::user();
        $roles = $user->getRoleNames();

        if (! $user) {
            return redirect()->route('login');
        }

        $roles = $user->getRoleNames();

        if ($roles->contains('registrar')) {
            return redirect()->route('super-admin.dashboard');
        }

        if ($roles->contains('program head')) {
            return redirect()->route('admin.dashboard');
        }

        if ($roles->contains('alumni')) {
            $hasTracer = $user->tracerStudy()->exists();

            return redirect()->route($hasTracer ? 'alumni.dashboard' : 'form');
        }

        Auth::logout();
        return redirect()->route('login');
    }
};