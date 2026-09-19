<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app-super-admin')] class extends Component
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

    public function monthsToFirstJobLabel(): string
    {
        $value = $this->user?->tracerStudy?->civilStatusEmployment?->months_to_first_job;

        if (! $value) return '—';

        // Preserve numeric ranges like "4-6", only split on word hyphens
        return ucwords(preg_replace('/-(?=[a-zA-Z])/', ' ', $value));
    }
};
