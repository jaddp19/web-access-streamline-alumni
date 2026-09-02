<?php

namespace App\Livewire;

use App\Models\UserProfile;
use App\Models\WorkHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        return UserProfile::with('batch')
            ->where('user_id', $this->alumni->id)
            ->first();
    }

    #[Computed]
    public function avatarUrl()
    {
        if ($this->userProfile?->avatar) {
            return Storage::url($this->userProfile->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->alumni->name) . '&background=D4A537&color=123524';
    }

    #[Computed]
    public function workHistories()
    {
        return WorkHistory::with('company')
            ->where('user_id', $this->alumni->id)
            ->orderByDesc('date_hired')
            ->get();
    }

    #[Computed]
    public function profileCompletion()
    {
        $profile = $this->userProfile;

        $steps = [
            'avatar'       => filled($profile?->avatar),
            'location'     => filled($profile?->location),
            'batch'        => filled($profile?->batch_id),
            'work_history' => $this->workHistories->isNotEmpty(),
        ];

        $completed = count(array_filter($steps));
        $total     = count($steps);

        return [
            'steps'     => $steps,
            'completed' => $completed,
            'total'     => $total,
            'percent'   => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }
};
