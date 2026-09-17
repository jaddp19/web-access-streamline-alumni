<?php

use App\Models\UserProfile;
use App\Models\WorkHistory;
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
            'gender'         => $this->userProfile?->gender ?? $location['gender'] ?? null,
            'phone_number_1' => $this->userProfile?->contact_number_1 ?? null,
            'phone_number_2' => $this->userProfile?->contact_number_2 ?? null,
            'latitude'       => $location['latitude'] ?? null,
            'longitude'      => $location['longitude'] ?? null,
            'address'        => $location['address'] ?? null,
        ];
    }

    #[Computed]
    public function avatarUrl(): ?string
    {
        $avatar = $this->userProfile?->avatar;

        // No avatar → return null so the blade can show the initial fallback
        if (blank($avatar)) {
            return null;
        }

        // Full external URL (e.g. from ui-avatars.com) → use as-is
        if (filter_var($avatar, FILTER_VALIDATE_URL)) {
            return $avatar;
        }

        // Local storage path → resolve via Storage::url()
        return \Illuminate\Support\Facades\Storage::url($avatar);
    }

    #[Computed]
    public function workHistories()
    {
        return WorkHistory::query()
            ->where('user_id', $this->alumni->id)
            ->with(['company:id,company_name,company_logo,company_address'])
            ->orderByDesc('is_current_job')
            ->orderByDesc('date_hired')
            ->get();
    }

    /**
     * Compute how long the alumnus has been in a role.
     * NOTE: WorkHistory has no end_date column yet — every record is
     * treated as ongoing from date_hired. Add `date_ended` to the
     * migration if you want accurate tenure for past jobs.
     */
    public function durationLabel(WorkHistory $history): string
    {
        if (! $history->date_hired) {
            return '—';
        }

        $months = $history->date_hired->diffInMonths(now());

        if ($months < 1) {
            return 'Less than a month';
        }

        $years = intdiv($months, 12);
        $rem   = $months % 12;

        $parts = [];
        if ($years > 0) $parts[] = $years . ' yr' . ($years > 1 ? 's' : '');
        if ($rem > 0)   $parts[] = $rem . ' mo' . ($rem > 1 ? 's' : '');

        return implode(' ', $parts);
    }
};
