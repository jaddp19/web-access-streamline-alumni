<?php

use App\Models\User;
use Illuminate\Support\Str;
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

    public function monthsToFirstJobLabel(): string
    {
        $value = $this->user->tracerStudy?->civilStatusEmployment?->months_to_first_job;

        if (! $value) {
            return '—';
        }

        return [
            '1-3-months'         => '1–3 Months',
            '4-6-months'         => '4–6 Months',
            'more-than-6-months' => 'More than 6 Months',
            'more-than-1-year'   => 'More than 1 Year',
            'not-yet-employed'   => 'Not yet employed',
        ][$value] ?? Str::headline($value);
    }
};