<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Course;
use App\Support\ReportsAnalytics;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

new #[Title('Reports')] #[Layout('layouts.app-super-admin')] class extends Component
{
    #[Url]
    public string $tab = 'employment';

    #[Url]
    public $selectedBatchId = '';

    #[Url]
    public string $dateRange = 'all';

    #[Url]
    public $selectedDepartmentId = '';

    #[Url]
    public $selectedCourseId = '';

    protected ?ReportsAnalytics $reportInstance = null;

    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['employment', 'industry', 'engagement', 'board_passers'], true)) {
            return;
        }

        $this->tab = $tab;
    }

    public function updatedSelectedBatchId(): void
    {
        $this->reportInstance = null;
    }

    protected function report(): ReportsAnalytics
    {
        return $this->reportInstance ??= new ReportsAnalytics(
            $this->selectedBatchId ? (int) $this->selectedBatchId : null,
            $this->selectedDepartmentId ? (int) $this->selectedDepartmentId : null,
            $this->selectedCourseId ? (int) $this->selectedCourseId : null,
        );
    }

    public function exportTracerCsv(): StreamedResponse
    {
        $rows = $this->report()->tracerStudyRows();

        $filename = 'tracer-study-'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'First Name', 'Middle Name', 'Last Name', 'Email', 'Batch',
                'Course Code', 'Course', 'Department',
                'Civil Status', 'Employment Status', 'Current Job Position', 'Current Company',
                'Employment Type', 'Organization Type', 'Employment Area', 'Country (if abroad)',
                'Months to First Job', 'Job Related to Degree',
                'Further Studies', 'Level of Study',
                'Board Exam Taken', 'Board Rating', 'Board Result',
                'Submitted At',
            ]);

            foreach ($rows as $r) {
                $boardResult = '';
                if (! empty($r['board_taken']) && $r['board_rate'] !== null && $r['board_rate'] !== '') {
                    $boardResult = ((float) $r['board_rate']) >= 75 ? 'Passed' : 'Failed';
                }

                fputcsv($handle, [
                    $r['first_name'] ?? '',
                    $r['middle_name'] ?? '',
                    $r['last_name'] ?? '',
                    $r['email'] ?? '',
                    $r['batch_name'] ?? '',
                    $r['course_code'] ?? '',
                    $r['course_title'] ?? '',
                    $r['dept_name'] ?? '',
                    $r['civil_status'] ?? '',
                    $r['employment_status'] ?? '',
                    $r['current_job_position'] ?? '',
                    $r['company_name'] ?? '',
                    $r['employment_type'] ?? '',
                    $r['organization_type'] ?? '',
                    $r['employment_area'] ?? '',
                    $r['abroad_country'] ?? '',
                    $r['months_to_first_job'] ?? '',
                    $r['employed_related_to_degree'] ?? '',
                    ! empty($r['is_pursued_further_studies']) ? 'Yes' : 'No',
                    $r['level_of_study'] ?? '',
                    $r['board_taken'] ?? '',
                    $r['board_rate'] ?? '',
                    $boardResult,
                    $r['submitted_at'] ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportRsvpCsv(): StreamedResponse
    {
        $rows = $this->report()->engagementRows();

        $filename = 'event-rsvps-'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Event Title', 'Event Date', 'Location', 'Capacity',
                'First Name', 'Middle Name', 'Last Name', 'Email', 'Batch',
                'Response', 'Responded At', 'Attended At', 'Notes',
            ]);

            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r['event_title'] ?? '',
                    $r['event_date'] ?? '',
                    $r['event_location'] ?? '',
                    $r['event_capacity'] ?? '',
                    $r['attendee_first_name'] ?? '',
                    $r['attendee_middle_name'] ?? '',
                    $r['attendee_last_name'] ?? '',
                    $r['attendee_email'] ?? '',
                    $r['batch_name'] ?? '',
                    $r['response'] ?? '',
                    $r['responded_at'] ?? '',
                    $r['attended_at'] ?? '',
                    $r['notes'] ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportBoardPassersCsv(): StreamedResponse
    {
        $rows = $this->report()->boardPassers();

        $filename = 'board-passers-'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'First Name', 'Middle Name', 'Last Name', 'Email',
                'Batch', 'Course Code', 'Course', 'Department',
                'Attempt', 'Date Taken', 'Rating',
                'Top Notcher', 'Top Notcher Rank', 'Verified At',
            ]);

            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r['first_name'] ?? '',
                    $r['middle_name'] ?? '',
                    $r['last_name'] ?? '',
                    $r['email'] ?? '',
                    $r['batch_name'] ?? '',
                    $r['course_code'] ?? '',
                    $r['course_title'] ?? '',
                    $r['dept_name'] ?? '',
                    $r['attempt_number'] ?? '',
                    $r['date_taken'] ?? '',
                    $r['rate'] ?? '',
                    ! empty($r['is_top_notcher']) ? 'Yes' : 'No',
                    $r['top_notcher_rank'] ?? '',
                    $r['verified_at'] ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * User picked a course → align the department filter to match it.
     * Prevents the nonsensical "course in dept A + department B" query.
     */
    public function updatedSelectedCourseId(): void
    {
        if ($this->selectedCourseId) {
            $course = Course::query()->find((int) $this->selectedCourseId);

            if ($course) {
                $this->selectedDepartmentId = (string) $course->department_id;
            }
        }

        $this->reportInstance = null;
    }

    /**
     * User picked a department → if the current course doesn't belong
     * to it, clear the course. Keeps the filter pair consistent.
     */
    public function updatedSelectedDepartmentId(): void
    {
        if ($this->selectedCourseId) {
            $belongs = Course::query()
                ->whereKey((int) $this->selectedCourseId)
                ->where('department_id', (int) $this->selectedDepartmentId)
                ->exists();

            if (! $belongs) {
                $this->selectedCourseId = '';
            }
        }

        $this->reportInstance = null;
    }

    // ===== Filters =====

    #[Computed]
    public function batches(): array
    {
        return $this->report()->batches();
    }

    #[Computed]
    public function totalAlumni(): int
    {
        return $this->report()->totalAlumni();
    }

    // ===== Report 1 — Employment Stats =====

    #[Computed]
    public function employmentRows(): array
    {
        return $this->report()->employmentStats();
    }

    #[Computed]
    public function departments(): array
    {
        return $this->report()->departments();
    }

    #[Computed]
    public function courses(): array
    {
        return $this->report()->courses();
    }

    #[Computed]
    public function employmentTotals(): array
    {
        return $this->report()->employmentStatsTotals($this->employmentRows);
    }

    // ===== Report 2 — Industry / Employers =====

    #[Computed]
    public function topEmployers(): array
    {
        return $this->report()->topEmployers();
    }

    #[Computed]
    public function industryDistribution(): array
    {
        return $this->report()->industryDistribution();
    }

    #[Computed]
    public function industryTotal(): int
    {
        return array_sum($this->industryDistribution);
    }

    // ===== Report 3 — Engagement =====

    #[Computed]
    public function engagementRows(): array
    {
        return $this->report()->engagementRates($this->dateRange);
    }

    #[Computed]
    public function engagementTotals(): array
    {
        $rows = $this->engagementRows;

        $t = ['events' => count($rows), 'yes' => 0, 'maybe' => 0, 'no' => 0, 'pending' => 0];
        foreach ($rows as $r) {
            $t['yes'] += $r['yes'];
            $t['maybe'] += $r['maybe'];
            $t['no'] += $r['no'];
            $t['pending'] += $r['pending'];
        }

        return $t;
    }

    // ===== Report 4 — Board Passers & Top Notchers =====

    #[Computed]
    public function boardPassers(): array
    {
        return $this->report()->boardPassers();
    }

    #[Computed]
    public function boardPasserSummary(): array
    {
        return $this->report()->boardPasserSummary();
    }

    // ===== Print header label =====

    #[Computed]
    public function reportLabel(): string
    {
        return match ($this->tab) {
            'employment'    => 'Yearly Alumni Employment Statistics',
            'industry'      => 'Top Industries & Employers of Alumni',
            'engagement'    => 'Alumni Engagement Participation Rates',
            'board_passers' => 'Alumni Board Passers & Top Notchers',
            default         => 'Report',
        };
    }

    #[Computed]
    public function batchLabel(): ?string
    {
        if (! $this->selectedBatchId) {
            return null;
        }

        return collect($this->batches)->firstWhere('id', (int) $this->selectedBatchId)['batch_name'] ?? null;
    }
};