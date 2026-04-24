<?php

use App\Models\AlumniProfile;
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
        return AlumniProfile::with('batch:id,batch_year')
            ->where('user_id', $this->alumni->id)
            ->first(); // returns null if no profile
    }


};
