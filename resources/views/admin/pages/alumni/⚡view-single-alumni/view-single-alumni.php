<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app-admin')] class extends Component
{
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user->load([
            'roles',
            'userProfile.batch',
            'userProfile.courses.department',
            'workHistories.company',
            'tracerStudy.furtherStudy',
            'tracerStudy.civilStatusEmployment',
        ]);
    }
};