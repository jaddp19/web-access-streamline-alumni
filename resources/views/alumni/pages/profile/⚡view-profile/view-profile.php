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
        return Auth::User();
    }

    #[Computed]
    public function alumniProfile()
    {
        return AlumniProfile::where('user_id', $this->alumni->id)
            ->first(); // returns null if no profile for super-admin view
    }

    #[Computed]
    public function educationalBackground()
    {
        if (! $this->alumniProfile) {
        return null;
        }
        
        return EducationalBackground::where('alumni_profile_id', $this->alumniProfile->id)
            ->first(); // returns null if no educational background for super-admin view
    }


};
