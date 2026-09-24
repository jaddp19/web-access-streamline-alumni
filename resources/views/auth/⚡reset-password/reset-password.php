<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    protected function rules(): array
    {
        return [
            'token'    => ['required'],
            'email'    => ['required', 'email:rfc', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required'     => 'Email is required.',
            'email.email'        => 'Please enter a valid email address.',
            'password.required'  => 'Password is required.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'The two passwords do not match.',
        ];
    }

    public function resetPassword()
    {
        $this->validate();

        $status = Password::broker()->reset(
            [
                'email'                 => $this->email,
                'password'              => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token'                 => $this->token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('success', 'Your password has been reset. You can now sign in with your new password.');
            return redirect()->route('login');
        }

        $message = match ($status) {
            Password::INVALID_TOKEN => 'This password reset link is invalid or has already been used.',
            Password::INVALID_USER  => 'We could not find an account with that email.',
            default                 => 'Could not reset your password. Please try again.',
        };

        $this->addError('email', $message);
    }
};