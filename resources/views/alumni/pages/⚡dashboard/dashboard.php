<?php

use App\Models\AlumniProfile;
use App\Models\EducationalBackground;
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
    public function alumniProfile()
    {
        return AlumniProfile::where('user_id', $this->alumni->id)->first();
    }

    #[Computed]
    public function educationalBackground()
    {
        if (! $this->alumniProfile) {
            return null;
        }

        return EducationalBackground::where('alumni_profile_id', $this->alumniProfile->id)->first();
    }

    #[Computed]
    public function profileCompletion()
    {
        $steps = [
            'personal_info' => (bool) $this->alumniProfile,
            'education'     => (bool) $this->educationalBackground,
        ];

        $completed = count(array_filter($steps));
        $total     = count($steps);

        return [
            'steps'     => $steps,
            'completed' => $completed,
            'total'     => $total,
            'percent'   => $total > 0 ? round(($completed / $total) * 100) : 0,
        ];
    }
};
