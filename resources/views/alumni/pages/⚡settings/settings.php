<?php

namespace App\Livewire;

use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

        // is_private stored inverted, so "visible" = !is_private
        $this->profileVisible = ! ($this->userProfile?->is_private ?? false);
    }

    #[Computed]
    public function userProfile()
    {
        return UserProfile::where('user_id', Auth::id())->first();
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function updateProfile()
    {
        $validated = $this->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $validated['name']  = $this->sanitizeData($validated['name']);
        $validated['email'] = $this->sanitizeData($validated['email']);

        Auth::user()->update($validated);

        session()->flash('profile_success', 'Profile updated successfully.');
    }

    public function updatePassword()
    {
        $validated = $this->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            $this->addError('current_password', 'Your current password is incorrect.');
            return;
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        $this->reset('current_password', 'new_password', 'new_password_confirmation');

        session()->flash('password_success', 'Password updated successfully.');
    }

    public function savePreferences()
    {
        $profile = $this->userProfile;

        if ($profile) {
            $profile->update([
                'is_private' => ! $this->profileVisible,
            ]);

            unset($this->userProfile);
        }

        // NOTE: emailNotifications and eventNotifications currently have no backing
        // columns in the user_profiles table. To persist these, a migration is required
        // to add 'email_notifications' and 'event_notifications' boolean columns.

        session()->flash('preferences_success', 'Preferences saved.');
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }
};
