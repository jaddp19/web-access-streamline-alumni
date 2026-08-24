<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
            'current_password'         => 'required|string',
            'new_password'              => 'required|string|min:6|confirmed',
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
        // Placeholder — wire up to a real preferences table/column when one exists.
        session()->flash('preferences_success', 'Preferences saved.');
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }
};
