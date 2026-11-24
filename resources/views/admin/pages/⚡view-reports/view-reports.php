<?php

namespace App\Livewire\Admin;

use App\Models\Department;
use App\Support\ReportsAnalytics;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

new #[Layout('layouts.app-admin')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $tab = 'employment';

    #[Url]
    public $selectedBatchId = '';

    #[Url]
    public string $dateRange = 'all';

    #[Url]
    public $selectedCourseId = '';

    protected int $perPage = 10;

    protected ?ReportsAnalytics $reportInstance = null;

    public function setTab(string $tab): void
    {
        if (! in_array($tab, ['employment', 'industry', 'engagement', 'board_passers'], true)) {
            return;
        }

        $this->tab = $tab;
        $this->resetPage();
    }

    public function updatedSelectedBatchId(): void
    {
        $this->reportInstance = null;
        $this->resetPage();
    }

    public function updatedDateRange(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCourseId(): void
    {
        $this->reportInstance = null;
        $this->resetPage();
    }

    #[Computed]
    public function scope(): int|false|null
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }
        if ($user->hasRole('alumni coordinator')) {
            return null;
        }

        return Department::where('program_head_id', $user->id)->value('id') ?? false;
    }

    #[Computed]
    public function isAlumniCoordinator(): bool
    {
        return Auth::user()?->hasRole('alumni coordinator') ?? false;
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

    // ===== Report 1 — Employment Stats (PAGINATED) =====

    #[Computed]
    public function employmentRows(): LengthAwarePaginator
    {
        if ($this->hasNoDepartment) {
            return $this->paginateArray([]);
        }

        return $this->paginateArray($this->report()->employmentStats());
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

        // Totals must use the FULL dataset, not the paginated slice.
        return $this->report()->employmentStatsTotals($this->report()->employmentStats());
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

    // ===== Report 3 — Engagement (PAGINATED) =====

    #[Computed]
    public function engagementRows(): LengthAwarePaginator
    {
        if ($this->hasNoDepartment) {
            return $this->paginateArray([]);
        }

        return $this->paginateArray($this->report()->engagementRates($this->dateRange));
    }

    #[Computed]
    public function engagementTotals(): array
    {
        // Totals must use the FULL dataset, not the paginated slice.
        $rows = $this->hasNoDepartment
            ? []
            : $this->report()->engagementRates($this->dateRange);

        $t = ['events' => count($rows), 'yes' => 0, 'maybe' => 0, 'no' => 0, 'pending' => 0];
        foreach ($rows as $r) {
            $t['yes'] += $r['yes'];
            $t['maybe'] += $r['maybe'];
            $t['no'] += $r['no'];
            $t['pending'] += $r['pending'];
        }

        return $t;
    }

    // ===== Report 4 — Board Passers & Top Notchers (PAGINATED) =====

    #[Computed]
    public function boardPassers(): LengthAwarePaginator
    {
        if ($this->hasNoDepartment) {
            return $this->paginateArray([]);
        }

        return $this->paginateArray($this->report()->boardPassers());
    }

    #[Computed]
    public function boardPasserSummary(): array
    {
        if ($this->hasNoDepartment) {
            return [
                'verified_alumni' => 0,
                'passed_alumni' => 0,
                'top_notcher_alumni' => 0,
                'pass_rate' => 0.0,
            ];
        }

        // Summary stats come from the FULL dataset, not the page.
        return $this->report()->boardPasserSummary();
    }

    // ===== Generate the report label for print header =====

    #[Computed]
    public function reportLabel(): string
    {
        return match ($this->tab) {
            'employment' => 'Yearly Alumni Employment Statistics',
            'industry' => 'Top Industries & Employers of Alumni',
            'engagement' => 'Alumni Engagement Participation Rates',
            'board_passers' => 'Alumni Board Passers & Top Notchers',
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

    /**
     * Wraps a plain array into a LengthAwarePaginator for the current page.
     * Employment, Engagement, and Board Passers all use this so they share
     * the same per-page config and page-count logic.
     *
     * Also clamps the current page into the valid range so that if the
     * underlying data shrinks (live filter change, department unassigned,
     * or a bookmark to ?page=5 that no longer exists), the user lands on
     * the last valid page instead of seeing an empty table.
     */
    protected function paginateArray(array $rows): LengthAwarePaginator
    {
        $total = count($rows);
        $lastPage = max(1, (int) ceil($total / $this->perPage));

        $page = max(1, min($this->getPage(), $lastPage));

        if ($page !== $this->getPage()) {
            $this->setPage($page);
        }

        $offset = ($page - 1) * $this->perPage;

        return new LengthAwarePaginator(
            collect($rows)->slice($offset, $this->perPage)->values(),
            $total,
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    #[Computed]
    public function employmentRowsForPrint(): array
    {
        return $this->hasNoDepartment ? [] : $this->report()->employmentStats();
    }

    #[Computed]
    public function engagementRowsForPrint(): array
    {
        return $this->hasNoDepartment ? [] : $this->report()->engagementRates($this->dateRange);
    }

    #[Computed]
    public function boardPassersForPrint(): array
    {
        return $this->hasNoDepartment ? [] : $this->report()->boardPassers();
    }
};
