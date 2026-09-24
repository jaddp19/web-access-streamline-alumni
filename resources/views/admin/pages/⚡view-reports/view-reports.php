<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use App\Support\ReportsAnalytics;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

new #[Layout('layouts.app-admin')] class extends Component
{
    #[Url]
    public string $tab = 'employment';

    #[Url]
    public $selectedBatchId = '';

    #[Url]
    public string $dateRange = 'all';

    #[Url]
    public $selectedCourseId = '';

    protected ?ReportsAnalytics $reportInstance = null;

    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['employment', 'industry', 'engagement'], true)) {
            return;
        }

        $this->tab = $tab;
    }

    public function updatedSelectedBatchId(): void
    {
        $this->reportInstance = null;
    }

    public function updatedDateRange(): void
    {
        // Only engagement uses this; no need to reset the whole thing
    }

    #[Computed]
    public function scope(): int|false|null
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('registrar')) {
            return null;
        }

        return Department::where('program_head_id', $user->id)->value('id') ?? false;
    }

    #[Computed]
    public function isRegistrar(): bool
    {
        return Auth::user()?->hasRole('registrar') ?? false;
    }

    #[Computed]
    public function hasNoDepartment(): bool
    {
        return $this->scope === false;
    }

    #[Computed]
    public function myDepartmentName(): ?string
    {
        return is_int($this->scope)
            ? Department::whereKey($this->scope)->value('dept_name')
            : null;
    }

    protected function report(): ReportsAnalytics
    {
        $deptId = is_int($this->scope) ? $this->scope : null;

        return $this->reportInstance ??= new ReportsAnalytics(
            $this->selectedBatchId ? (int) $this->selectedBatchId : null,
            $deptId,
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

    public function updatedSelectedCourseId(): void
    {
        $this->reportInstance = null;
    }

    // ===== Filters =====

    #[Computed]
    public function batches(): array
    {
        if ($this->hasNoDepartment) {
            return [];
        }

        return $this->report()->batches();
    }

    #[Computed]
    public function totalAlumni(): int
    {
        if ($this->hasNoDepartment) {
            return 0;
        }

        return $this->report()->totalAlumni();
    }

    // ===== Report 1 — Employment Stats =====

    #[Computed]
    public function employmentRows(): array
    {
        if ($this->hasNoDepartment) {
            return [];
        }

        return $this->report()->employmentStats();
    }

    #[Computed]
    public function courses(): array
    {
        return $this->hasNoDepartment ? [] : $this->report()->courses();
    }

    #[Computed]
    public function employmentTotals(): array
    {
        if ($this->hasNoDepartment) {
            return [
                'total' => 0, 'employed' => 0, 'unemployed' => 0,
                'self_employed' => 0, 'other' => 0, 'employment_rate' => 0.0,
            ];
        }

        return $this->report()->employmentStatsTotals($this->employmentRows);
    }

    // ===== Report 2 — Industry / Employers =====

    #[Computed]
    public function topEmployers(): array
    {
        if ($this->hasNoDepartment) {
            return [];
        }

        return $this->report()->topEmployers();
    }

    #[Computed]
    public function industryDistribution(): array
    {
        if ($this->hasNoDepartment) {
            return [];
        }

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
        if ($this->hasNoDepartment) {
            return [];
        }

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

    // ===== Generate the report label for print header =====

    #[Computed]
    public function reportLabel(): string
    {
        return match ($this->tab) {
            'employment' => 'Yearly Alumni Employment Statistics',
            'industry' => 'Top Industries & Employers of Alumni',
            'engagement' => 'Alumni Engagement Participation Rates',
            default => 'Report',
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
