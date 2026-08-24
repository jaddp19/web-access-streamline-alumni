<?php

// TODO: AlumniProfile model was removed. Reimplement against UserProfile + WorkHistory.
// TODO: EducationalBackground model was removed. Reimplement against UserProfile + WorkHistory.
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
        // TODO: AlumniProfile model was removed. Reimplement against UserProfile.
        return null;
    }

    #[Computed]
    public function educationalBackground()
    {
        // TODO: EducationalBackground model was removed. Reimplement against UserProfile + WorkHistory.
        return null;
    }
};
