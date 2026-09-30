<?php

use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public User $user;

    public bool $showRejectModal = false;
    public string $rejectionReason = '';

    public function mount(User $user): void
    {
        Gate::authorize('can_view_any');
        $this->user = $user->load([
            'roles:id,name',

            'userProfile:id,user_id,batch_id,avatar,is_verified,is_private,is_approved,last_rejection_reason,gender,contact_number_1,contact_number_2,location,board_taken,board_rate',
            'userProfile.batch:id,batch_name',

            // ── Board exam attempts — only VERIFIED ones ──
            'userProfile.boardExams' => fn ($q) => $q->where('is_verified', true)
                                                      ->orderByDesc('date_taken'),

            'userProfile.courses' => fn ($q) => $q->orderBy('course_title'),
            'userProfile.courses:id,course_title,course_code,course_type,department_id',
            'userProfile.courses.department:id,dept_name',

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

    // =========================================================
    //  APPROVAL WORKFLOW
    // =========================================================

    public function approve(): void
    {
        $profile = $this->user->userProfile;

        if (! $profile) {
            session()->flash('error', 'This alumni has no profile to approve.');
            return;
        }

        $profile->update([
            'is_approved'           => true,
            'last_rejection_reason' => null,
        ]);

        $this->refreshProfile();

        EmailTemplateService::send('profile-approved', $this->user->email, [
            'name'        => $this->user->name,
            'approved_at' => now()->format('F j, Y · g:i A'),
            'approved_by' => Auth::user()?->name ?? 'the registrar',
        ]);

        session()->flash('success', 'Profile approved. An email has been sent to the alumni.');
    }

    public function openRejectModal(): void
    {
        $this->rejectionReason = '';
        $this->resetErrorBag('rejectionReason');
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->rejectionReason = '';
        $this->resetErrorBag('rejectionReason');
    }

    public function reject(): void
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5|max:1000',
        ], [
            'rejectionReason.required' => 'Please explain why this profile is being rejected.',
            'rejectionReason.min'      => 'Please provide a more detailed reason (at least 5 characters).',
            'rejectionReason.max'      => 'The reason cannot exceed 1000 characters.',
        ]);

        $profile = $this->user->userProfile;

        if (! $profile) {
            session()->flash('error', 'This alumni has no profile to reject.');
            return;
        }

        $reason = trim(strip_tags($this->rejectionReason));

        $profile->update([
            'is_approved'           => false,
            'last_rejection_reason' => $reason,
        ]);

        $this->refreshProfile();

        EmailTemplateService::send('profile-rejected', $this->user->email, [
            'name'        => $this->user->name,
            'reason'      => $reason,
            'rejected_at' => now()->format('F j, Y · g:i A'),
            'rejected_by' => Auth::user()?->name ?? 'the registrar',
        ]);

        $this->showRejectModal = false;
        $this->rejectionReason = '';

        session()->flash('success', 'Profile rejected. The alumni has been notified by email.');
    }

    // =========================================================
    //  HELPERS
    // =========================================================

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

    /**
     * Reload just the profile so the badge and buttons reflect the
     * new approval state without a full page refresh.
     */
    protected function refreshProfile(): void
    {
        $this->user->refresh();
        $this->user->load([
            'userProfile:id,user_id,batch_id,avatar,is_verified,is_private,is_approved,last_rejection_reason,gender,contact_number_1,contact_number_2,location,board_taken,board_rate',

            // ── Only verified attempts after reload too ──
            'userProfile.boardExams' => fn ($q) => $q->where('is_verified', true)
                                                      ->orderByDesc('date_taken'),
        ]);
    }
};