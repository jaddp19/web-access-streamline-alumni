<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public function checkApprovalStatus()
    {
        $user = Auth::user();
        $user->refresh();

        if ($user->hasRole('alumni')) {
            $this->redirect(route('alumni.dashboard'), navigate: true);
        }
    }
};
