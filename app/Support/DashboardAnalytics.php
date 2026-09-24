<?php

namespace App\Support;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DashboardAnalytics
{
    public function __construct(
        protected ?int $batchId = null,
        /** null → global (registrar) · int → scoped to a department */
        protected ?int $departmentId = null,
    ) {}

    // =========================================================
    //  BATCHES (selector)
    // =========================================================

    public function batches(): array
    {
        $query = Batch::query()->orderByDesc('batch_name');

        if ($this->departmentId) {
            $query->whereHas('userProfiles', function ($p) {
                $p->whereHas('user', fn ($u) => $u->role('alumni'))
                    ->whereHas('courses', fn ($c) => $c->where('department_id', $this->departmentId));
            });
        }

        return $query
            ->get(['id', 'batch_name'])
            ->map(fn ($b) => ['id' => (int) $b->id, 'batch_name' => (string) $b->batch_name])
            ->toArray();
    }

    // =========================================================
    //  STAT CARDS
    // =========================================================

    public function users(): int
    {
        return User::query()
            ->when($this->departmentId, fn ($q) => $q->whereHas(
                'userProfile.courses',
                fn ($c) => $c->where('department_id', $this->departmentId)
            ))
            ->count();
    }

    public function alumni(): int
    {
        return $this->alumniBaseQuery()->count();
    }

    public function boardPassers(): int
    {
        // Delegate to the chart's breakdown so the stat card and the
        // Board Exam Performance chart always show the same number.
        return $this->boardExamBreakdown()['Passed'];
    }

    public function pendingVerifications(): int
    {
        return $this->profileBaseQuery()
            ->where('is_verified', false)
            ->whereNotNull('board_taken')
            ->whereNotNull('board_rate')
            ->whereHas('courses', fn ($c) => $c->where('course_type', 'board'))
            ->count();
    }

    public function courses(): int
    {
        return Course::query()
            ->where('is_active', true)
            ->when($this->departmentId, fn ($q) => $q->where('department_id', $this->departmentId))
            ->count();
    }

    public function programHeads(): int
    {
        return User::role('program head')
            ->when($this->departmentId, fn ($q) => $q->whereHas(
                'department',
                fn ($d) => $d->where('id', $this->departmentId)
            ))
            ->count();
    }

    // =========================================================
    //  BASE QUERIES (with dept + batch scope)
    // =========================================================

    protected function alumniBaseQuery()
    {
        return User::role('alumni')
            ->when($this->departmentId, fn ($q) => $q->whereHas(
                'userProfile.courses',
                fn ($c) => $c->where('department_id', $this->departmentId)
            ))
            ->when($this->batchId, fn ($q) => $q->whereHas(
                'userProfile',
                fn ($p) => $p->where('batch_id', $this->batchId)
            ));
    }

    protected function profileBaseQuery()
    {
        return UserProfile::query()
            ->when($this->departmentId, fn ($q) => $q->whereHas(
                'courses',
                fn ($c) => $c->where('department_id', $this->departmentId)
            ))
            ->when($this->batchId, fn ($q) => $q->where('batch_id', $this->batchId));
    }

    // =========================================================
    //  ALUMNI BY DEPARTMENT
    // =========================================================

    public function alumniByDept(): array
    {
        return DB::table('departments')
            ->join('courses', 'courses.department_id', '=', 'departments.id')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->where('departments.is_active', true)
            ->where('courses.is_active', true)
            ->when($this->departmentId, fn ($q) => $q->where('departments.id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId))
            ->select(
                'departments.id as dept_id',
                'departments.dept_name',
                'departments.dept_code',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total')
            )
            ->groupBy('departments.id', 'departments.dept_name', 'departments.dept_code')
            ->orderBy('departments.dept_name')
            ->get()
            ->mapWithKeys(fn ($r) => [
                $r->dept_code ?: "DEPT-{$r->dept_id}" => [
                    'name' => $r->dept_name,
                    'total' => (int) $r->total,
                ],
            ])
            ->toArray();
    }

    // =========================================================
    //  ALUMNI BY DEPARTMENT → COURSES (drill-down)
    // =========================================================

    /**
     * Nested: department → courses, with totals at both levels.
     * Shape:
     *   [
     *     'IT' => [
     *       'name'    => 'Information Technology',
     *       'total'   => 75,
     *       'courses' => [
     *         'BSIT' => ['name' => 'BS Information Technology', 'total' => 50],
     *         'BSCS' => ['name' => 'BS Computer Science',       'total' => 25],
     *       ],
     *     ],
     *   ]
     */
    public function alumniByDeptAndCourse(): array
    {
        $rows = DB::table('departments')
            ->join('courses', 'courses.department_id', '=', 'departments.id')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->where('departments.is_active', true)
            ->where('courses.is_active', true)
            ->when($this->departmentId, fn ($q) => $q->where('departments.id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId))
            ->select(
                'departments.id as dept_id',
                'departments.dept_code',
                'departments.dept_name',
                'courses.id as course_id',
                'courses.course_code',
                'courses.course_title',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total')
            )
            ->groupBy(
                'departments.id',
                'departments.dept_code',
                'departments.dept_name',
                'courses.id',
                'courses.course_code',
                'courses.course_title'
            )
            ->orderBy('departments.dept_name')
            ->orderBy('courses.course_code')
            ->get();

        $result = [];

        foreach ($rows as $row) {
            $deptKey = $row->dept_code ?: "DEPT-{$row->dept_id}";

            if (! isset($result[$deptKey])) {
                $result[$deptKey] = [
                    'name' => $row->dept_name,
                    'total' => 0,
                    'courses' => [],
                ];
            }

            $courseKey = $row->course_code ?: "COURSE-{$row->course_id}";
            $count = (int) $row->total;

            $result[$deptKey]['courses'][$courseKey] = [
                'name' => $row->course_title,
                'total' => $count,
            ];
            $result[$deptKey]['total'] += $count;
        }

        return $result;
    }

    // =========================================================
    //  ALUMNI BY BATCH
    // =========================================================

    public function alumniByBatch(): array
    {
        return DB::table('user_profiles')
            ->join('batches', 'user_profiles.batch_id', '=', 'batches.id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->when($this->departmentId, fn ($q) => $q->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('student_course')
                    ->join('courses', 'courses.id', '=', 'student_course.course_id')
                    ->whereColumn('student_course.user_profile_id', 'user_profiles.id')
                    ->where('courses.department_id', $this->departmentId);
            }))
            ->select(
                'batches.id as batch_id',
                'batches.batch_name',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total')
            )
            ->groupBy('batches.id', 'batches.batch_name')
            ->orderBy('batches.batch_name')
            ->get()
            ->mapWithKeys(fn ($r) => [
                (string) $r->batch_id => [
                    'batch_name' => $r->batch_name,
                    'total' => (int) $r->total,
                ],
            ])
            ->toArray();
    }

    // =========================================================
    //  ALUMNI BY BATCH → COURSES (drill-down)
    // =========================================================

    /**
     * Nested: batch → courses, with totals at both levels.
     * Shape:
     *   [
     *     '1' => [
     *       'batch_name' => '2020',
     *       'total'      => 45,
     *       'courses'    => [
     *         'BSIT' => ['name' => 'BS Information Technology', 'total' => 25],
     *         'BSCS' => ['name' => 'BS Computer Science',       'total' => 20],
     *       ],
     *     ],
     *   ]
     */
    public function alumniByBatchAndCourse(): array
    {
        $rows = DB::table('batches')
            ->join('user_profiles', 'user_profiles.batch_id', '=', 'batches.id')
            ->join('student_course', 'user_profiles.id', '=', 'student_course.user_profile_id')
            ->join('courses', 'courses.id', '=', 'student_course.course_id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->where('courses.is_active', true)
            ->when($this->departmentId, fn ($q) => $q->where('courses.department_id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId))
            ->select(
                'batches.id as batch_id',
                'batches.batch_name',
                'courses.id as course_id',
                'courses.course_code',
                'courses.course_title',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total')
            )
            ->groupBy(
                'batches.id',
                'batches.batch_name',
                'courses.id',
                'courses.course_code',
                'courses.course_title'
            )
            ->orderBy('batches.batch_name')
            ->orderBy('courses.course_code')
            ->get();

        $result = [];

        foreach ($rows as $row) {
            $batchKey = (string) $row->batch_id;

            if (! isset($result[$batchKey])) {
                $result[$batchKey] = [
                    'batch_name' => $row->batch_name,
                    'total' => 0,
                    'courses' => [],
                ];
            }

            $courseKey = $row->course_code ?: "COURSE-{$row->course_id}";
            $count = (int) $row->total;

            $result[$batchKey]['courses'][$courseKey] = [
                'name' => $row->course_title,
                'total' => $count,
            ];
            $result[$batchKey]['total'] += $count;
        }

        return $result;
    }

    // =========================================================
    //  COURSE ANALYTICS (single query)
    // =========================================================

    public function courseAnalytics(): array
    {
        $rows = DB::table('courses')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->join('tracer_studies', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->join('civil_status_employments', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
            ->where('courses.is_active', true)
            ->where('civil_status_employments.employment_status', 'employed')
            ->when($this->departmentId, fn ($q) => $q->where('courses.department_id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId))
            ->select(
                'courses.course_code',
                DB::raw('COUNT(DISTINCT CASE
                    WHEN civil_status_employments.employed_related_to_degree IN (\'yes\', \'partially-related\')
                    THEN user_profiles.user_id END) as related_count'),
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as employed_total')
            )
            ->groupBy('courses.id', 'courses.course_code')
            ->havingRaw('COUNT(DISTINCT user_profiles.user_id) > 0')
            ->orderBy('courses.course_code')
            ->get();

        return $rows->map(fn ($r) => [
            'course_code' => $r->course_code,
            'related_rate' => (int) round(($r->related_count / $r->employed_total) * 100),
        ])->all();
    }

    public function courseAnalyticsByDept(): array
    {
        $rows = DB::table('departments')
            ->join('courses', 'courses.department_id', '=', 'departments.id')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->join('tracer_studies', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->join('civil_status_employments', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
            ->where('departments.is_active', true)
            ->where('courses.is_active', true)
            ->where('civil_status_employments.employment_status', 'employed')
            ->when($this->departmentId, fn ($q) => $q->where('departments.id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId))
            ->select(
                'departments.id as dept_id',
                'departments.dept_code',
                'departments.dept_name',
                'courses.id as course_id',
                'courses.course_code',
                'courses.course_title',
                DB::raw("COUNT(DISTINCT CASE WHEN civil_status_employments.employed_related_to_degree IN ('yes', 'partially-related') THEN user_profiles.user_id END) as related_count"),
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as employed_total')
            )
            ->groupBy(
                'departments.id',
                'departments.dept_code',
                'departments.dept_name',
                'courses.id',
                'courses.course_code',
                'courses.course_title'
            )
            ->orderBy('departments.dept_name')
            ->orderBy('courses.course_code')
            ->get();

        $result = [];

        foreach ($rows as $row) {
            $deptKey = $row->dept_code ?: "DEPT-{$row->dept_id}";

            if (! isset($result[$deptKey])) {
                $result[$deptKey] = [
                    'name' => $row->dept_name,
                    'total' => 0,
                    'related_count' => 0,
                    'related_rate' => 0,
                    'courses' => [],
                ];
            }

            $total = (int) $row->employed_total;
            $related = (int) $row->related_count;
            $rate = $total > 0 ? (int) round(($related / $total) * 100) : 0;

            $courseKey = $row->course_code ?: "COURSE-{$row->course_id}";

            $result[$deptKey]['courses'][$courseKey] = [
                'name' => $row->course_title,
                'total' => $total,
                'related_count' => $related,
                'related_rate' => $rate,
            ];

            $result[$deptKey]['total'] += $total;
            $result[$deptKey]['related_count'] += $related;
        }

        // Compute aggregate alignment rate per department
        foreach ($result as &$dept) {
            $dept['related_rate'] = $dept['total'] > 0
                ? (int) round(($dept['related_count'] / $dept['total']) * 100)
                : 0;
        }
        unset($dept);

        return $result;
    }

    // =========================================================
    //  TRACER BREAKDOWNS (GROUP BY in SQL)
    // =========================================================

    public function tracerBreakdowns(): array
    {
        return [
            'employment_status' => $this->groupedCount('employment_status'),
            'employment_type' => $this->groupedCount('employment_type'),
            'organization_type' => $this->groupedCount('organization_type'),
            'employment_area' => $this->groupedCount('employment_area'),
            'civil_status' => $this->groupedCount('civil_status'),
            'job_alignment' => $this->groupedCount('employed_related_to_degree'),
            'months_to_first_job' => $this->monthsBreakdown(),
        ];
    }

    protected function tracerQuery(): Builder
    {
        $q = DB::table('civil_status_employments')
            ->join('tracer_studies', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
            ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id');

        if ($this->departmentId) {
            $q->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('student_course')
                    ->join('courses', 'courses.id', '=', 'student_course.course_id')
                    ->whereColumn('student_course.user_profile_id', 'user_profiles.id')
                    ->where('courses.department_id', $this->departmentId);
            });
        }

        if ($this->batchId) {
            $q->where('user_profiles.batch_id', $this->batchId);
        }

        return $q;
    }

    protected function groupedCount(string $column): array
    {
        $rows = $this->tracerQuery()
            ->select($column, DB::raw('COUNT(*) as total'))
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->groupBy($column)
            ->pluck('total', $column)
            ->toArray();

        return array_map('intval', $rows);
    }

    protected function monthsBreakdown(): array
    {
        $counts = $this->groupedCount('months_to_first_job');
        $order = ['1-3-months', '4-6-months', 'more-than-6-months', 'more-than-1-year'];

        $out = [];
        foreach ($order as $key) {
            $out[$key] = $counts[$key] ?? 0;
        }

        return $out;
    }

    // =========================================================
    //  FURTHER STUDIES
    // =========================================================

    public function furtherStudiesRate(): int
    {
        $row = $this->furtherStudiesQuery()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_pursued_further_studies = 1 THEN 1 ELSE 0 END) as yes')
            ->first();

        if (! $row || ! $row->total) {
            return 0;
        }

        return (int) round(($row->yes / $row->total) * 100);
    }

    public function furtherStudiesLevelBreakdown(): array
    {
        return $this->furtherStudiesQuery()
            ->where('is_pursued_further_studies', true)
            ->whereNotNull('level_of_study')
            ->select('level_of_study', DB::raw('COUNT(*) as total'))
            ->groupBy('level_of_study')
            ->pluck('total', 'level_of_study')
            ->map(fn ($v) => (int) $v)
            ->toArray();
    }

    protected function furtherStudiesQuery(): Builder
    {
        $q = DB::table('further_studies')
            ->join('tracer_studies', 'further_studies.tracer_study_id', '=', 'tracer_studies.id')
            ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id');

        if ($this->departmentId) {
            $q->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('student_course')
                    ->join('courses', 'courses.id', '=', 'student_course.course_id')
                    ->whereColumn('student_course.user_profile_id', 'user_profiles.id')
                    ->where('courses.department_id', $this->departmentId);
            });
        }

        if ($this->batchId) {
            $q->where('user_profiles.batch_id', $this->batchId);
        }

        return $q;
    }

    // =========================================================
    //  NEW ANALYTICS — all SQL-aggregated, no PHP loops
    // =========================================================

    public function genderBreakdown(): array
    {
        return $this->profileBaseQuery()
            ->whereNotNull('gender')
            ->selectRaw('gender, COUNT(*) as total')
            ->groupBy('gender')
            ->pluck('total', 'gender')
            ->map(fn ($v) => (int) $v)
            ->mapWithKeys(fn ($v, $k) => [ucfirst(strtolower($k)) => $v])
            ->toArray();
    }

    public function alumniByRegion(): array
    {
        $driver = DB::connection()->getDriverName();
        $expr = $driver === 'sqlite'
            ? "json_extract(location, '$.region_name')"
            : "JSON_UNQUOTE(JSON_EXTRACT(location, '$.region_name'))";

        return $this->profileBaseQuery()
            ->whereNotNull('location')
            ->selectRaw("{$expr} as region_name, COUNT(*) as total")
            ->groupByRaw($expr)
            ->orderByDesc('total')
            ->limit(8)
            ->pluck('total', 'region_name')
            ->filter(fn ($v, $k) => filled($k))
            ->map(fn ($v) => (int) $v)
            ->toArray();
    }

    public function boardExamBreakdown(): array
    {
        $row = $this->profileBaseQuery()
            ->whereNotNull('board_taken')
            ->selectRaw('COUNT(*) as taken')
            ->selectRaw('SUM(CASE WHEN board_rate >= 75 THEN 1 ELSE 0 END) as passed')
            ->first();

        $taken = (int) ($row->taken ?? 0);
        $passed = (int) ($row->passed ?? 0);

        return [
            'Passed' => $passed,
            'Failed' => max(0, $taken - $passed),
        ];
    }

    public function topEmployers(): array
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

        if ($this->departmentId) {
            $q->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('student_course')
                    ->join('courses', 'courses.id', '=', 'student_course.course_id')
                    ->whereColumn('student_course.user_profile_id', 'user_profiles.id')
                    ->where('courses.department_id', $this->departmentId);
            });
        }

        if ($this->batchId) {
            $q->where('user_profiles.batch_id', $this->batchId);
        }

        return $q->select('companies.company_name', DB::raw('COUNT(*) as total'))
            ->groupBy('companies.id', 'companies.company_name')
            ->orderByDesc('total')
            ->limit(8)
            ->pluck('total', 'company_name')
            ->map(fn ($v) => (int) $v)
            ->toArray();
    }

    // =========================================================
    //  CHART PAYLOAD
    // =========================================================

    public function chartPayload(): array
    {
        $tracer = $this->tracerBreakdowns();

        return [
            'alumniByDept' => $this->alumniByDept(),
            'alumniByDeptAndCourse' => $this->alumniByDeptAndCourse(),
            'alumniByBatch' => $this->alumniByBatch(),
            'alumniByBatchAndCourse' => $this->alumniByBatchAndCourse(),
            'courseAnalytics' => $this->courseAnalytics(),
            'employmentStatus' => $tracer['employment_status'],
            'employmentType' => $tracer['employment_type'],
            'organizationType' => $tracer['organization_type'],
            'employmentArea' => $tracer['employment_area'],
            'civilStatus' => $tracer['civil_status'],
            'jobAlignment' => $tracer['job_alignment'],
            'monthsToFirstJob' => $tracer['months_to_first_job'],
            'gender' => $this->genderBreakdown(),
            'furtherStudies' => $this->furtherStudiesLevelBreakdown(),
            'boardExam' => $this->boardExamBreakdown(),
            'topEmployers' => $this->topEmployers(),
            'alumniByRegion' => $this->alumniByRegion(),
        ];
    }
}
