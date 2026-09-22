<?php

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        // Eager load everything the view touches — one batch of queries,
        // no N+1 when rendering courses / work / tracer / department.
        $this->user = $user->load([
            'roles:id,name',

            'userProfile:id,user_id,batch_id,avatar,is_verified,is_private,gender,contact_number_1,contact_number_2,location,board_taken,board_rate',
            'userProfile.batch:id,batch_name',

            // Order the courses so the render is deterministic
            'userProfile.courses' => fn ($q) => $q->orderBy('course_title'),
            'userProfile.courses:id,course_title,course_code,course_type,department_id',
            'userProfile.courses.department:id,dept_name',

            // Newest job first
            'workHistories' => fn ($q) => $q->orderByDesc('is_current_job')
                                              ->orderByDesc('date_hired')
                                              ->orderByDesc('id'),
            'workHistories:id,user_id,work_name,date_hired,is_current_job,company_id',
            'workHistories.company:id,company_name,company_logo',

            'tracerStudy:id,user_id',
            'tracerStudy.furtherStudy',
            'tracerStudy.civilStatusEmployment',
        ]);
    }

    public function monthsToFirstJobLabel(): string
    {
        $value = $this->user?->tracerStudy?->civilStatusEmployment?->months_to_first_job;

        if (! $value) {
            return '—';
        }

        // Preserve numeric ranges like "4-6", only split on word hyphens
        return ucwords(preg_replace('/-(?=[a-zA-Z])/', ' ', $value));
    }
};