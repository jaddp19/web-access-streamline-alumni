<?php

namespace App\Support;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ReportsAnalytics
{
    public function __construct(
        protected ?int $batchId = null,
        protected ?int $departmentId = null,
        protected ?int $courseId = null,
    ) {}

    // =========================================================
    //  FILTER OPTIONS
    // =========================================================

    public function batches(): array
    {
        $q = Batch::query()->orderByDesc('batch_name');

        if ($this->departmentId || $this->courseId) {
            $q->whereHas('userProfiles', function ($p) {
                $p->whereHas('user', fn ($u) => $u->role('alumni'));

                if ($this->courseId) {
                    $p->whereHas('courses', fn ($c) => $c->where('courses.id', $this->courseId));
                }

                if ($this->departmentId) {
                    $p->whereHas('courses', fn ($c) => $c->where('courses.department_id', $this->departmentId));
                }
            });
        }

        return $q->get(['batches.id', 'batches.batch_name'])
            ->map(fn ($b) => ['id' => (int) $b->id, 'batch_name' => (string) $b->batch_name])
            ->toArray();
    }

    public function departments(): array
    {
        $q = Department::query()->orderBy('dept_name');

        if ($this->departmentId) {
            $q->where('departments.id', $this->departmentId);
        }

        return $q->get(['departments.id', 'departments.dept_name', 'departments.dept_code'])
            ->map(fn ($d) => [
                'id'        => (int) $d->id,
                'dept_name' => (string) $d->dept_name,
                'dept_code' => (string) $d->dept_code,
            ])
            ->toArray();
    }

    public function courses(): array
    {
        $q = Course::query()
            ->where('is_active', true)
            ->orderBy('course_title');

        if ($this->departmentId) {
            $q->where('courses.department_id', $this->departmentId);
        }

        return $q->get(['courses.id', 'courses.course_title', 'courses.course_code', 'courses.department_id'])
            ->map(fn ($c) => [
                'id'            => (int) $c->id,
                'course_title'  => (string) $c->course_title,
                'course_code'   => (string) $c->course_code,
                'department_id' => (int) $c->department_id,
            ])
            ->toArray();
    }

    public function totalAlumni(): int
    {
        return $this->alumniScopeQuery()->count();
    }

    // =========================================================
    //  SHARED PROFILE SCOPE
    // =========================================================

    protected function scopeByProfile($q, string $profileAlias = 'user_profiles'): void
    {
        if ($this->departmentId) {
            $q->whereExists(function ($sub) use ($profileAlias) {
                $sub->select(DB::raw(1))
                    ->from('student_course')
                    ->join('courses', 'courses.id', '=', 'student_course.course_id')
                    ->whereColumn('student_course.user_profile_id', "$profileAlias.id")
                    ->where('courses.department_id', $this->departmentId);
            });
        }

        if ($this->courseId) {
            $q->whereExists(function ($sub) use ($profileAlias) {
                $sub->select(DB::raw(1))
                    ->from('student_course')
                    ->whereColumn('student_course.user_profile_id', "$profileAlias.id")
                    ->where('student_course.course_id', $this->courseId);
            });
        }

        if ($this->batchId) {
            $q->where("$profileAlias.batch_id", $this->batchId);
        }
    }

    protected function alumniScopeQuery(): Builder
    {
        $q = DB::table('user_profiles')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni');

        $this->scopeByProfile($q);

        return $q;
    }

    // =========================================================
    //  REPORT 1 — YEARLY EMPLOYMENT STATISTICS
    // =========================================================

    public function employmentStats(): array
    {
        $q = DB::table('batches')
            ->join('user_profiles', 'user_profiles.batch_id', '=', 'batches.id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->leftJoin('tracer_studies', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->leftJoin('civil_status_employments', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id');

        $this->scopeByProfile($q);

        $rows = $q->select(
                'batches.id as batch_id',
                'batches.batch_name',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total'),
                DB::raw("COUNT(DISTINCT CASE WHEN civil_status_employments.employment_status = 'employed' THEN user_profiles.user_id END) as employed"),
                DB::raw("COUNT(DISTINCT CASE WHEN civil_status_employments.employment_status = 'unemployed' THEN user_profiles.user_id END) as unemployed"),
                DB::raw("COUNT(DISTINCT CASE WHEN civil_status_employments.employment_status = 'self-employed' THEN user_profiles.user_id END) as self_employed"),
                DB::raw("COUNT(DISTINCT CASE WHEN civil_status_employments.employment_status = 'other' THEN user_profiles.user_id END) as other_status")
            )
            ->groupBy('batches.id', 'batches.batch_name')
            ->orderByDesc('batches.batch_name')
            ->get();

        return $rows->map(function ($r) {
            $total    = (int) $r->total;
            $employed = (int) $r->employed;
            $rate     = $total > 0 ? round(($employed / $total) * 100, 1) : 0.0;

            return [
                'batch_name'      => $r->batch_name,
                'total'           => $total,
                'employed'        => $employed,
                'unemployed'      => (int) $r->unemployed,
                'self_employed'   => (int) $r->self_employed,
                'other'           => (int) $r->other_status,
                'employment_rate' => $rate,
            ];
        })->all();
    }

    public function employmentStatsTotals(array $rows): array
    {
        $totals = [
            'total' => 0, 'employed' => 0, 'unemployed' => 0,
            'self_employed' => 0, 'other' => 0, 'employment_rate' => 0.0,
        ];

        foreach ($rows as $row) {
            $totals['total']         += $row['total'];
            $totals['employed']      += $row['employed'];
            $totals['unemployed']    += $row['unemployed'];
            $totals['self_employed'] += $row['self_employed'];
            $totals['other']         += $row['other'];
        }

        $totals['employment_rate'] = $totals['total'] > 0
            ? round(($totals['employed'] / $totals['total']) * 100, 1)
            : 0.0;

        return $totals;
    }

    // =========================================================
    //  REPORT 2 — TOP INDUSTRIES & EMPLOYERS
    // =========================================================

    public function topEmployers(int $limit = 15): array
    {
        $q = DB::table('work_histories')
            ->join('companies', 'work_histories.company_id', '=', 'companies.id')
            ->join('user_profiles', 'work_histories.user_id', '=', 'user_profiles.user_id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->whereNotNull('work_histories.company_id');

        $this->scopeByProfile($q);

        return $q->select(
                'companies.id as company_id',
                'companies.company_name',
                'companies.company_address',
                DB::raw('COUNT(DISTINCT work_histories.user_id) as total')
            )
            ->groupBy('companies.id', 'companies.company_name', 'companies.company_address')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => [
                'company_name'    => $r->company_name,
                'company_address' => $r->company_address,
                'total'           => (int) $r->total,
            ])
            ->all();
    }

    public function industryDistribution(): array
    {
        $q = DB::table('civil_status_employments')
            ->join('tracer_studies', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
            ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->where('civil_status_employments.employment_status', 'employed')
            ->whereNotNull('civil_status_employments.organization_type')
            ->where('civil_status_employments.organization_type', '!=', '');

        $this->scopeByProfile($q);

        return $q->select('civil_status_employments.organization_type', DB::raw('COUNT(*) as total'))
            ->groupBy('civil_status_employments.organization_type')
            ->orderByDesc('total')
            ->pluck('total', 'organization_type')
            ->map(fn ($v) => (int) $v)
            ->toArray();
    }

    // =========================================================
    //  REPORT 3 — ENGAGEMENT
    // =========================================================

    public function engagementRates(string $dateRange = 'all'): array
    {
        $totalAlumni = $this->totalAlumni();
        if ($totalAlumni === 0) {
            return [];
        }

        $q = DB::table('events')
            ->leftJoin('event_rsvps', 'event_rsvps.event_id', '=', 'events.id')
            ->where('events.status', 'published');

        if ($this->departmentId) {
            $q->whereIn('events.created_by', function ($sub) {
                $sub->select('users.id')
                    ->from('users')
                    ->join('departments', 'departments.program_head_id', '=', 'users.id')
                    ->where('departments.id', $this->departmentId);
            });
        }

        if ($this->courseId) {
            $q->where(function ($w) {
                $w->whereNull('event_rsvps.id')
                    ->orWhereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('user_profiles')
                            ->join('student_course', 'student_course.user_profile_id', '=', 'user_profiles.id')
                            ->whereColumn('user_profiles.user_id', 'event_rsvps.user_id')
                            ->where('student_course.course_id', $this->courseId);
                    });
            });
        }

        match ($dateRange) {
            '30'  => $q->where('events.starts_at', '>=', now()->subDays(30)),
            '90'  => $q->where('events.starts_at', '>=', now()->subDays(90)),
            '180' => $q->where('events.starts_at', '>=', now()->subDays(180)),
            '365' => $q->where('events.starts_at', '>=', now()->subDays(365)),
            default => null,
        };

        $rows = $q->select(
                'events.id',
                'events.title',
                'events.starts_at',
                'events.capacity',
                DB::raw("COUNT(DISTINCT CASE WHEN event_rsvps.response = 'yes' THEN event_rsvps.user_id END) as yes_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN event_rsvps.response = 'maybe' THEN event_rsvps.user_id END) as maybe_count"),
                DB::raw("COUNT(DISTINCT CASE WHEN event_rsvps.response = 'no' THEN event_rsvps.user_id END) as no_count")
            )
            ->groupBy('events.id', 'events.title', 'events.starts_at', 'events.capacity')
            ->orderByDesc('events.starts_at')
            ->get();

        return $rows->map(function ($r) use ($totalAlumni) {
            $yes       = (int) $r->yes_count;
            $maybe     = (int) $r->maybe_count;
            $no        = (int) $r->no_count;
            $responded = $yes + $maybe + $no;
            $pending   = max(0, $totalAlumni - $responded);

            return [
                'title'         => $r->title,
                'starts_at'     => $r->starts_at,
                'capacity'      => $r->capacity,
                'invited'       => $totalAlumni,
                'yes'           => $yes,
                'maybe'         => $maybe,
                'no'            => $no,
                'pending'       => $pending,
                'participation' => $totalAlumni > 0 ? round(($yes / $totalAlumni) * 100, 1) : 0.0,
            ];
        })->all();
    }

    // =========================================================
    //  CSV EXPORT ROWS
    // =========================================================

    public function tracerStudyRows(): array
    {
        $q = DB::table('tracer_studies')
            ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->join('users', 'users.id', '=', 'user_profiles.user_id')
            ->leftJoin('batches', 'user_profiles.batch_id', '=', 'batches.id')
            ->leftJoin('civil_status_employments', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
            ->leftJoin('further_studies', 'further_studies.tracer_study_id', '=', 'tracer_studies.id')
            ->leftJoin('student_course', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->leftJoin('courses', 'courses.id', '=', 'student_course.course_id')
            ->leftJoin('departments', 'departments.id', '=', 'courses.department_id')
            ->leftJoin('work_histories', function ($join) {
                $join->on('work_histories.user_id', '=', 'user_profiles.user_id')
                     ->where('work_histories.is_current_job', '=', true);
            })
            ->leftJoin('companies', 'companies.id', '=', 'work_histories.company_id');

        if ($this->departmentId) {
            $q->where('departments.id', $this->departmentId);
        }
        if ($this->courseId) {
            $q->where('courses.id', $this->courseId);
        }
        if ($this->batchId) {
            $q->where('user_profiles.batch_id', $this->batchId);
        }

        return $q->select(
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'users.email',
                'batches.batch_name',
                'courses.course_code',
                'courses.course_title',
                'departments.dept_name',
                'civil_status_employments.civil_status',
                'civil_status_employments.employment_status',
                'civil_status_employments.current_job_position',
                'companies.company_name',
                'civil_status_employments.employment_type',
                'civil_status_employments.organization_type',
                'civil_status_employments.employment_area',
                'civil_status_employments.abroad_country',
                'civil_status_employments.months_to_first_job',
                'civil_status_employments.employed_related_to_degree',
                'further_studies.is_pursued_further_studies',
                'further_studies.level_of_study',
                'user_profiles.board_taken',
                'user_profiles.board_rate',
                'tracer_studies.updated_at as submitted_at'
            )
            ->orderBy('users.name')
            ->get()
            ->map(fn ($r) => (array) $r)
            ->all();
    }

    public function engagementRows(): array
    {
        $q = DB::table('event_rsvps')
            ->join('events', 'event_rsvps.event_id', '=', 'events.id')
            ->join('users', 'event_rsvps.user_id', '=', 'users.id')
            ->leftJoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('batches', 'user_profiles.batch_id', '=', 'batches.id')
            ->where('events.status', 'published');

        if ($this->departmentId) {
            $q->whereIn('events.created_by', function ($sub) {
                $sub->select('users.id')
                    ->from('users')
                    ->join('departments', 'departments.program_head_id', '=', 'users.id')
                    ->where('departments.id', $this->departmentId);
            });
        }

        if ($this->courseId) {
            $q->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('student_course')
                    ->whereColumn('student_course.user_profile_id', 'user_profiles.id')
                    ->where('student_course.course_id', $this->courseId);
            });
        }

        if ($this->batchId) {
            $q->where('user_profiles.batch_id', $this->batchId);
        }

        return $q->select(
                'events.title as event_title',
                'events.starts_at as event_date',
                'events.location as event_location',
                'events.capacity as event_capacity',
                'users.first_name as attendee_first_name',
                'users.middle_name as attendee_middle_name',
                'users.last_name as attendee_last_name',
                'users.email as attendee_email',
                'batches.batch_name',
                'event_rsvps.response',
                'event_rsvps.responded_at',
                'event_rsvps.attended_at',
                'event_rsvps.notes'
            )
            ->orderBy('events.starts_at', 'desc')
            ->orderBy('users.name')
            ->get()
            ->map(fn ($r) => (array) $r)
            ->all();
    }
}