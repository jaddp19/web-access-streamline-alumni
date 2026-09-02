<?php

use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-alumni')] class extends Component
{
    #[Computed]
    public function alumni()
    {
        return Auth::user();
    }

    #[Computed]
    public function userProfile()
    {
        return UserProfile::with(['batch', 'courses.department'])
            ->where('user_id', $this->alumni->id)
            ->first();
    }

    #[Computed]
    public function course()
    {
        return $this->userProfile?->courses->first();
    }

    /**
     * Safely decode the profile's `location` column into an array,
     * whether it comes back as a cast array or a raw JSON string.
     */
    protected function decodeLocation(): array
    {
        if (! $this->userProfile) {
            return [];
        }

        if (is_array($this->userProfile->location)) {
            return $this->userProfile->location;
        }

        if (is_string($this->userProfile->location)) {
            $decoded = json_decode($this->userProfile->location, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    #[Computed]
    public function contact()
    {
        $location = $this->decodeLocation();

        return [
            'gender'          => $location['gender'] ?? null,
            'phone_number_1'  => $location['phone_number_1'] ?? null,
            'phone_number_2'  => $location['phone_number_2'] ?? null,
            'latitude'        => $location['latitude'] ?? null,
            'longitude'       => $location['longitude'] ?? null,
            'address'         => $location['address'] ?? null,
        ];
    }

    #[Computed]
    public function avatarUrl()
    {
        if ($this->userProfile?->avatar) {
            return \Illuminate\Support\Facades\Storage::url($this->userProfile->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->alumni->name) . '&background=D4A537&color=123524&size=256';
    }
};
