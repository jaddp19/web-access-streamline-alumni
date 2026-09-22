<?php

namespace App\Livewire;

use App\Models\UserProfile;
use App\Support\SettingsRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

new #[Layout('layouts.app-settings')] class extends Component
{
    public string $activeTab = 'profile';

    // Profile info — split into parts
    public string $first_name  = '';
    public string $middle_name = '';
    public string $last_name   = '';
    public string $email       = '';

    // Password
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // Preferences
    public bool $emailNotifications = true;
    public bool $eventNotifications = true;
    public bool $profileVisible     = true;

    #[Locked]
    public array $allowedTabs = ['profile', 'password', 'preferences', 'appearance'];

    public function mount(): void
    {
        $user = Auth::user();

        [$first, $middle, $last] = $this->resolveNameParts($user);

        $this->first_name  = $first;
        $this->middle_name = $middle;
        $this->last_name   = $last;

        $this->email = $user->email;

        $this->profileVisible = ! ($this->userProfile?->is_private ?? false);
    }

    // ===== Computed =====

    #[Computed]
    public function userProfile(): ?UserProfile
    {
        return UserProfile::where('user_id', Auth::id())->first();
    }

    #[Computed]
    public function fullName(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])));
    }

    // ===== Tabs =====

    public function setTab(string $tab): void
    {
        if (! in_array($tab, $this->allowedTabs, true)) {
            return;
        }

        $this->activeTab = $tab;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    // ===== Profile =====

    public function updateProfile(): void
    {
        $validated = $this->validate(
            SettingsRules::profile(),
            SettingsRules::messages()
        );

        $first  = $this->sanitizeData($validated['first_name']);
        $middle = $validated['middle_name'] ? $this->sanitizeData($validated['middle_name']) : null;
        $last   = $this->sanitizeData($validated['last_name']);
        $email  = Str::lower($this->sanitizeData($validated['email']));

        Auth::user()->update([
            'first_name'  => $first,
            'middle_name' => $middle,
            'last_name'   => $last,
            'email'       => $email,
            // 'name' auto-fills via the User model's saving hook
        ]);

        // Refresh local form with sanitized values
        $this->first_name  = $first;
        $this->middle_name = $middle ?? '';
        $this->last_name   = $last;
        $this->email       = $email;

        session()->flash('profile_success', 'Profile updated successfully.');
    }

    // ===== Password =====

    public function updatePassword(): void
    {
        $validated = $this->validate(
            SettingsRules::password(),
            SettingsRules::messages()
        );

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

    // ===== Preferences =====

    public function savePreferences(): void
    {
        $validated = $this->validate(
            SettingsRules::preferences(),
            SettingsRules::messages()
        );

        if ($profile = $this->userProfile) {
            $profile->update([
                'is_private' => ! $validated['profileVisible'],
            ]);

            unset($this->userProfile);
        }

        session()->flash('preferences_success', 'Preferences saved.');
    }

    // ===== Helpers =====

    /**
     * Resolve name parts, splitting from `name` if the parts are legacy-empty.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    protected function resolveNameParts($user): array
    {
        $first  = $user->first_name;
        $middle = $user->middle_name;
        $last   = $user->last_name;

        if (! $first && ! $last && $user->name) {
            $split  = preg_split('/\s+/', trim($user->name));
            $first  = $split[0] ?? '';
            $last   = count($split) > 1 ? end($split) : '';
            $middle = count($split) > 2 ? implode(' ', array_slice($split, 1, -1)) : '';
        }

        return [$first ?? '', $middle ?? '', $last ?? ''];
    }

    protected function sanitizeData($data)
    {
        return is_string($data)
            ? Str::of($data)->stripTags()->trim()->toString()
            : $data;
    }
};