<?php

namespace App\Livewire;

use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-alumni')] class extends Component
{
    public string $activeTab = 'profile';

    // Profile info
    public string $name = '';
    public string $email = '';

    // Password
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // Preferences
    public bool $emailNotifications = true;
    public bool $eventNotifications = true;
    public bool $profileVisible = true;

    public function mount()
    {
        $user = Auth::user();
        $this->name  = $user->name;
        $this->email = $user->email;

        $this->profileVisible = ! ($this->userProfile?->is_private ?? false);
    }

    #[Computed]
    public function userProfile()
    {
        return UserProfile::where('user_id', Auth::id())->first();
    }

    /**
     * Switch tabs and clear any lingering validation errors.
     */
    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    protected function profileRules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\p{L}\p{M}\s\.\-\'\,]+$/u', // letters, marks, spaces, . - ' ,
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email,' . Auth::id(),
            ],
        ];
    }

    protected function profileMessages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'name.min'      => 'Your name must be at least 2 characters.',
            'name.max'      => 'Your name cannot exceed 255 characters.',
            'name.regex'    => 'Your name can only contain letters, spaces, and basic punctuation (. - \' ,).',
            'email.required' => 'Please enter your email address.',
            'email.email'    => 'Please enter a valid email address (e.g. juan@gmail.com).',
            'email.unique'   => 'This email is already registered to another account.',
            'email.max'      => 'Your email cannot exceed 255 characters.',
        ];
    }

    public function updateProfile()
    {
        $validated = $this->validate($this->profileRules(), $this->profileMessages());

        $validated['name']  = $this->sanitizeData($validated['name']);
        $validated['email'] = Str::lower($this->sanitizeData($validated['email']));

        Auth::user()->update($validated);

        // Refresh the local form values with the sanitized versions
        $this->name  = $validated['name'];
        $this->email = $validated['email'];

        session()->flash('profile_success', 'Profile updated successfully.');
    }

    protected function passwordRules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password'     => [
                'required',
                'string',
                'confirmed',         
                'different:current_password',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ];
    }

    protected function passwordMessages(): array
    {
        return [
            'current_password.required' => 'Please enter your current password.',
            'new_password.required'     => 'Please enter a new password.',
            'new_password.confirmed'    => 'New password and confirmation do not match.',
            'new_password.different'    => 'Your new password must be different from your current password.',
            'new_password.min'          => 'Your new password must be at least 8 characters.',
            'new_password.letters'      => 'Your new password must contain at least one letter.',
            'new_password.mixed'        => 'Your new password must contain both uppercase and lowercase letters.',
            'new_password.numbers'      => 'Your new password must contain at least one number.',
            'new_password.symbols'      => 'Your new password must contain at least one symbol (e.g. !@#$%).',
        ];
    }

    public function updatePassword()
    {
        $validated = $this->validate($this->passwordRules(), $this->passwordMessages());

        $user = Auth::user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            $this->addError('current_password', 'Your current password is incorrect.');
            return;
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        $this->reset('current_password', 'new_password', 'new_password_confirmation');
        $this->resetErrorBag();

        session()->flash('password_success', 'Password updated successfully.');
    }

    public function savePreferences()
    {
        $validated = $this->validate([
            'emailNotifications' => 'required|boolean',
            'eventNotifications' => 'required|boolean',
            'profileVisible'     => 'required|boolean',
        ], [
            'emailNotifications.required' => 'Email notifications setting is required.',
            'emailNotifications.boolean'  => 'Email notifications must be on or off.',
            'eventNotifications.required' => 'Event alerts setting is required.',
            'eventNotifications.boolean'  => 'Event alerts must be on or off.',
            'profileVisible.required'     => 'Profile visibility setting is required.',
            'profileVisible.boolean'      => 'Profile visibility must be on or off.',
        ]);

        $profile = $this->userProfile;

        if ($profile) {
            $profile->update([
                'is_private' => ! $validated['profileVisible'],
            ]);

            unset($this->userProfile);
        }

        // NOTE: emailNotifications & eventNotifications are not yet backed by DB
        // columns. Add these in a migration to persist them:
        //   $table->boolean('email_notifications')->default(true);
        //   $table->boolean('event_notifications')->default(true);

        session()->flash('preferences_success', 'Preferences saved.');
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }
};