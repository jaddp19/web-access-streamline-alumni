<?php

namespace App\Services;

use App\Models\FormHistory;
use App\Models\User;
use App\Models\UserProfile;

class FormHistoryService
{
    /**
     * Save an immutable copy of the alumni's current form after it has been
     * accepted by a Program Head or Alumni Coordinator.
     *
     * Only approved forms call this service. Pending edits never create a
     * history row because update-form simply mutates the live rows.
     */
    public static function capture(UserProfile $profile, ?User $approvedBy = null): ?FormHistory
    {
        $user = $profile->user;

        if (! $user) {
            return null;
        }

        // Refresh every piece of the form so the snapshot reflects exactly
        // what was approved, not stale relations held by the Livewire page.
        $user->load([
            'userProfile.batch:id,batch_name',
            'userProfile.courses:id,course_title,course_type',
            'userProfile.boardExams',
            'tracerStudy.civilStatusEmployment',
            'tracerStudy.furtherStudy',
            'workHistories' => fn ($query) => $query
                ->orderByDesc('is_current_job')
                ->orderByDesc('date_hired')
                ->orderByDesc('id'),
            'workHistories.company:id,company_name',
        ]);

        $profile = $user->userProfile;

        if (! $profile) {
            return null;
        }

        $snapshot = self::buildSnapshot($user, $profile);
        $hash = hash('sha256', json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $latest = FormHistory::query()
            ->where('user_id', $user->id)
            ->orderByDesc('version')
            ->first();

        // A double-clicked approval, or re-approving the same unchanged data,
        // must not create duplicate history entries.
        if ($latest && hash_equals($latest->snapshot_hash, $hash)) {
            return $latest;
        }

        return FormHistory::create([
            'user_id' => $user->id,
            'approved_by' => $approvedBy?->id,
            'version' => ((int) FormHistory::query()->where('user_id', $user->id)->max('version')) + 1,
            'approved_at' => now(),
            'snapshot' => $snapshot,
            'snapshot_hash' => $hash,
        ]);
    }

    /**
     * Build the complete, render-ready copy of the form.
     */
    protected static function buildSnapshot(User $user, UserProfile $profile): array
    {
        $location = $profile->location ?? [];
        $employment = $user->tracerStudy?->civilStatusEmployment;
        $furtherStudy = $user->tracerStudy?->furtherStudy;

        return [
            'personal' => [
                'name' => $user->full_name ?: $user->name,
                'email' => $user->email,
                'gender' => $profile->gender,
                'contact_number_1' => $profile->contact_number_1,
                'contact_number_2' => $profile->contact_number_2,
                'address' => $location['address'] ?? null,
                'address_type' => $location['address_type'] ?? null,
                'street_address' => $location['street_address'] ?? null,
                'region_name' => $location['region_name'] ?? null,
                'province_name' => $location['province_name'] ?? null,
                'city_name' => $location['city_name'] ?? null,
                'barangay_name' => $location['barangay_name'] ?? null,
                'intl_country' => $location['intl_country'] ?? null,
                'intl_state' => $location['intl_state'] ?? null,
                'intl_city' => $location['intl_city'] ?? null,
                'batch' => $profile->batch?->batch_name,
                'batch_id' => $profile->batch_id,
                'courses' => $profile->courses->map(fn ($course) => [
                    'id' => $course->id,
                    'title' => $course->course_title,
                    'type' => $course->course_type,
                ])->values()->all(),
                'board_exams' => $profile->boardExams->map(fn ($exam) => [
                    'exam_name' => $exam->exam_name,
                    'attempt_number' => $exam->attempt_number,
                    'date_taken' => $exam->date_taken?->format('Y-m-d'),
                    'rate' => $exam->rate !== null ? (float) $exam->rate : null,
                    'passed' => (bool) $exam->passed,
                    'is_verified' => (bool) $exam->is_verified,
                    'is_top_notcher' => (bool) $exam->is_top_notcher,
                    'top_notcher_rank' => $exam->top_notcher_rank,
                ])->values()->all(),
            ],

            'tracer' => [
                'civil_status' => $employment?->civil_status,
                'employment_status' => $employment?->employment_status,
                'employment_status_other' => $employment?->employment_status_other,
                'current_job_position' => $employment?->current_job_position,
                'employed_related_to_degree' => $employment?->employed_related_to_degree,
                'employment_type' => $employment?->employment_type,
                'employment_type_other' => $employment?->employment_type_other,
                'organization_type' => $employment?->organization_type,
                'organization_type_other' => $employment?->organization_type_other,
                'employment_area' => $employment?->employment_area,
                'abroad_country' => $employment?->abroad_country,
                'months_to_first_job' => $employment?->months_to_first_job,
            ],

            'further_study' => [
                'is_pursued_further_studies' => (bool) ($furtherStudy?->is_pursued_further_studies ?? false),
                'level_of_study' => $furtherStudy?->level_of_study,
            ],

            'work_histories' => $user->workHistories->map(fn ($work) => [
                'work_name' => $work->work_name,
                'company_name' => $work->company?->company_name,
                'date_hired' => $work->date_hired?->format('Y-m-d'),
                'is_current_job' => (bool) $work->is_current_job,
            ])->values()->all(),
        ];
    }
}
