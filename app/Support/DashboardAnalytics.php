<?php

namespace App\Support;

use App\Models\Batch;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Analytics are scoped to APPROVED + ACTIVE alumni only
 * (user_profiles.is_approved = true AND users.is_active = true).
 *
 * Pending / rejected / deactivated profiles are excluded from stats,
 * charts, and drill-downs so the dashboard always reflects reviewed,
 * official, currently-active data.
 *
 * Two intentional exceptions:
 *   • pendingVerifications() — counts alumni still awaiting review (workflow counter)
 *   • users() / courses() / programHeads() — system-wide metrics, not alumni data
 */
class DashboardAnalytics
{
    public function __construct(
        protected ?int $batchId = null,
        /** null → global (registrar) · int → scoped to a department */
        protected ?int $departmentId = null,
    ) {}

    // =========================================================
    //  ACTIVE-USER GATE (helper for raw-DB queries)
    // =========================================================

    /**
     * Restricts a raw-DB query to profiles whose owning user is active.
     * Use alongside an existing `is_approved` condition on the profile.
     */
    protected function requireActiveUser($q, string $profileAlias = 'user_profiles'): void
    {
        $q->whereExists(function ($sub) use ($profileAlias) {
            $sub->select(DB::raw(1))
                ->from('users')
                ->whereColumn('users.id', "{$profileAlias}.user_id")
                ->where('users.is_active', true);
        });
    }

    // =========================================================
    //  BATCHES (selector)
    // =========================================================

    public function batches(): array
    {
        $query = Batch::query()->orderByDesc('batch_name');

        if ($this->departmentId) {
            $query->whereHas('userProfiles', function ($p) {
                $p->where('is_approved', true)
                    ->whereHas('user', fn ($u) => $u->role('alumni')->where('is_active', true))
                    ->whereHas('courses', fn ($c) => $c->where('department_id', $this->departmentId));
            });
        } else {
            // Only show batches that contain at least one approved + active alumni profile
            $query->whereHas('userProfiles', function ($p) {
                $p->where('is_approved', true)
                    ->whereHas('user', fn ($u) => $u->where('is_active', true));
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

    /**
     * System-wide user count — intentionally NOT filtered by approval
     * so admins can still see how many accounts exist in total.
     * Also NOT filtered by is_active, so deactivated accounts are still
     * visible to admins in this total.
     */
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
        return $this->boardExamBreakdown()['Passed'];
    }

    /**
     * Alumni who have submitted board data but haven't been verified yet.
     * This is a WORKFLOW counter — it deliberately excludes BOTH the
     * is_approved and is_active filters, because pending items must be
     * visible to the registrar regardless of the user's current state.
     */
    public function pendingVerifications(): int
    {
        return UserProfile::query()
            ->where('is_approved', false)
            ->where('is_verified', false)
            ->whereNotNull('board_taken')
            ->whereNotNull('board_rate')
            ->whereHas('courses', fn ($c) => $c->where('course_type', 'board'))
            ->when($this->departmentId, fn ($q) => $q->whereHas(
                'courses',
                fn ($c) => $c->where('department_id', $this->departmentId)
            ))
            ->when($this->batchId, fn ($q) => $q->where('batch_id', $this->batchId))
            ->count();
    }

    /**
     * Active course catalog — system metric, not alumni data.
     */
    public function courses(): int
    {
        return Course::query()
            ->where('is_active', true)
            ->when($this->departmentId, fn ($q) => $q->where('department_id', $this->departmentId))
            ->count();
    }

    /**
     * Program head staff count — system metric, not alumni data.
     * Deactivated staff are excluded.
     */
    public function programHeads(): int
    {
        return User::role('program head')
            ->where('is_active', true)
            ->when($this->departmentId, fn ($q) => $q->whereHas(
                'department',
                fn ($d) => $d->where('id', $this->departmentId)
            ))
            ->count();
    }

    // =========================================================
    //  BASE QUERIES (with dept + batch + approval + active scope)
    // =========================================================

    protected function alumniBaseQuery()
    {
        return User::role('alumni')
            ->where('is_active', true)
            ->whereHas('userProfile', fn ($p) => $p->where('is_approved', true))
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
            ->where('is_approved', true)
            ->whereHas('user', fn ($u) => $u->where('is_active', true))
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
        $q = DB::table('departments')
            ->join('courses', 'courses.department_id', '=', 'departments.id')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->where('user_profiles.is_approved', true)
            ->where('departments.is_active', true)
            ->where('courses.is_active', true)
            ->when($this->departmentId, fn ($q) => $q->where('departments.id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId));

        $this->requireActiveUser($q);

        return $q->select(
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

    public function alumniByDeptAndCourse(): array
    {
        $q = DB::table('departments')
            ->join('courses', 'courses.department_id', '=', 'departments.id')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->where('user_profiles.is_approved', true)
            ->where('departments.is_active', true)
            ->where('courses.is_active', true)
            ->when($this->departmentId, fn ($q) => $q->where('departments.id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId));

        $this->requireActiveUser($q);

        $rows = $q->select(
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
                    'dept_code' => $row->dept_code ?: $deptKey,
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
        $q = DB::table('user_profiles')
            ->join('batches', 'user_profiles.batch_id', '=', 'batches.id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->where('user_profiles.is_approved', true)
            ->when($this->departmentId, fn ($q) => $q->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('student_course')
                    ->join('courses', 'courses.id', '=', 'student_course.course_id')
                    ->whereColumn('student_course.user_profile_id', 'user_profiles.id')
                    ->where('courses.department_id', $this->departmentId);
            }))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId));

        $this->requireActiveUser($q);

        return $q->select(
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

    public function alumniByBatchAndCourse(): array
    {
        $q = DB::table('batches')
            ->join('user_profiles', 'user_profiles.batch_id', '=', 'batches.id')
            ->join('student_course', 'user_profiles.id', '=', 'student_course.user_profile_id')
            ->join('courses', 'courses.id', '=', 'student_course.course_id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->where('user_profiles.is_approved', true)
            ->where('courses.is_active', true)
            ->when($this->departmentId, fn ($q) => $q->where('courses.department_id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId));

        $this->requireActiveUser($q);

        $rows = $q->select(
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
    //  ALUMNI BY BATCH → DEPARTMENTS → COURSES (3-level drill-down)
    // =========================================================

    public function alumniByBatchAndDepartment(): array
    {
        $q = DB::table('batches')
            ->join('user_profiles', 'user_profiles.batch_id', '=', 'batches.id')
            ->join('student_course', 'user_profiles.id', '=', 'student_course.user_profile_id')
            ->join('courses', 'courses.id', '=', 'student_course.course_id')
            ->join('departments', 'departments.id', '=', 'courses.department_id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->where('user_profiles.is_approved', true)
            ->where('departments.is_active', true)
            ->where('courses.is_active', true)
            ->when($this->departmentId, fn ($q) => $q->where('departments.id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId));

        $this->requireActiveUser($q);

        $rows = $q->select(
                'batches.id as batch_id',
                'batches.batch_name',
                'departments.id as dept_id',
                'departments.dept_code',
                'departments.dept_name',
                'courses.id as course_id',
                'courses.course_code',
                'courses.course_title',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total')
            )
            ->groupBy(
                'batches.id',
                'batches.batch_name',
                'departments.id',
                'departments.dept_code',
                'departments.dept_name',
                'courses.id',
                'courses.course_code',
                'courses.course_title'
            )
            ->orderBy('batches.batch_name')
            ->orderBy('departments.dept_name')
            ->orderBy('courses.course_code')
            ->get();

        $result = [];

        foreach ($rows as $row) {
            $batchKey = (string) $row->batch_id;
            $deptKey = $row->dept_code ?: "DEPT-{$row->dept_id}";
            $courseKey = $row->course_code ?: "COURSE-{$row->course_id}";
            $count = (int) $row->total;

            if (! isset($result[$batchKey])) {
                $result[$batchKey] = [
                    'batch_name' => $row->batch_name,
                    'total' => 0,
                    'departments' => [],
                ];
            }

            if (! isset($result[$batchKey]['departments'][$deptKey])) {
                $result[$batchKey]['departments'][$deptKey] = [
                    'name' => $row->dept_name,
                    'total' => 0,
                    'courses' => [],
                ];
            }

            $result[$batchKey]['departments'][$deptKey]['courses'][$courseKey] = [
                'name' => $row->course_title,
                'total' => $count,
            ];

            $result[$batchKey]['departments'][$deptKey]['total'] += $count;
            $result[$batchKey]['total'] += $count;
        }

        return $result;
    }

    // =========================================================
    //  COURSE ANALYTICS (single query)
    // =========================================================

    public function courseAnalytics(): array
    {
        $q = DB::table('courses')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->join('tracer_studies', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->join('civil_status_employments', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
            ->where('user_profiles.is_approved', true)
            ->where('courses.is_active', true)
            ->where('civil_status_employments.employment_status', 'employed')
            ->when($this->departmentId, fn ($q) => $q->where('courses.department_id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId));

        $this->requireActiveUser($q);

        $rows = $q->select(
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
            'related_rate' => round(($r->related_count / $r->employed_total) * 100, 2),
        ])->all();
    }

    public function courseAnalyticsByDept(): array
    {
        $q = DB::table('departments')
            ->join('courses', 'courses.department_id', '=', 'departments.id')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->join('tracer_studies', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->join('civil_status_employments', 'civil_status_employments.tracer_study_id', '=', 'tracer_studies.id')
            ->where('user_profiles.is_approved', true)
            ->where('departments.is_active', true)
            ->where('courses.is_active', true)
            ->where('civil_status_employments.employment_status', 'employed')
            ->when($this->departmentId, fn ($q) => $q->where('departments.id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId));

        $this->requireActiveUser($q);

        $rows = $q->select(
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
            $rate = $total > 0 ? round(($related / $total) * 100, 2) : 0;

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

        foreach ($result as &$dept) {
            $dept['related_rate'] = $dept['total'] > 0
                ? round(($dept['related_count'] / $dept['total']) * 100, 2)
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
            ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->where('user_profiles.is_approved', true);

        $this->requireActiveUser($q);

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
            ->join('user_profiles', 'tracer_studies.user_id', '=', 'user_profiles.user_id')
            ->where('user_profiles.is_approved', true);

        $this->requireActiveUser($q);

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
            ->where('is_verified', true)
            ->whereNotNull('board_taken')
            ->whereHas('courses', fn ($c) => $c->where('course_type', 'board'))
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
            ->where('user_profiles.is_approved', true)
            ->whereNotNull('work_histories.company_id')
            ->where('work_histories.is_current_job', true);

        $this->requireActiveUser($q);

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
            ->map(fn (int|string $v) => (int) $v)
            ->toArray();
    }

    // =========================================================
    //  TOP NOTCHERS — which dept/course produces them?
    // =========================================================

    /**
     * Nested: department → courses, with top-notcher counts and best rank.
     * Same shape as alumniByDeptAndCourse() so the JS drill-down
     * renderer can reuse the same pattern.
     *
     * Returns depts already sorted by total DESC (champion first).
     */
    public function topNotchersByDeptAndCourse(): array
    {
        $q = DB::table('departments')
            ->join('courses', 'courses.department_id', '=', 'departments.id')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->join('board_exams', 'board_exams.user_profile_id', '=', 'user_profiles.id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->where('user_profiles.is_approved', true)
            ->where('board_exams.is_top_notcher', true)
            ->where('board_exams.is_verified', true)
            ->where('departments.is_active', true)
            ->where('courses.is_active', true)
            ->when($this->departmentId, fn ($q) => $q->where('departments.id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId));

        $this->requireActiveUser($q);

        $rows = $q->select(
                'departments.id as dept_id',
                'departments.dept_code',
                'departments.dept_name',
                'courses.id as course_id',
                'courses.course_code',
                'courses.course_title',
                DB::raw('COUNT(DISTINCT board_exams.id) as total'),
                DB::raw('MIN(board_exams.top_notcher_rank) as best_rank'),
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
            $deptKey   = $row->dept_code ?: "DEPT-{$row->dept_id}";
            $courseKey = $row->course_code ?: "COURSE-{$row->course_id}";
            $count     = (int) $row->total;
            $bestRank  = $row->best_rank !== null ? (int) $row->best_rank : null;

            if (! isset($result[$deptKey])) {
                $result[$deptKey] = [
                    'name'      => $row->dept_name,
                    'total'     => 0,
                    'best_rank' => null,
                    'courses'   => [],
                ];
            }

            $result[$deptKey]['courses'][$courseKey] = [
                'name'      => $row->course_title,
                'total'     => $count,
                'best_rank' => $bestRank,
            ];

            $result[$deptKey]['total'] += $count;

            if ($bestRank !== null) {
                $existing = $result[$deptKey]['best_rank'];
                if ($existing === null || $bestRank < $existing) {
                    $result[$deptKey]['best_rank'] = $bestRank;
                }
            }
        }

        // Sort departments by total DESC (champion first).
        uasort($result, fn ($a, $b) => $b['total'] <=> $a['total']);

        return $result;
    }

    /**
     * Single highest-ranked top notcher in the current scope.
     * Rank 1 = best. Returns null when no verified top notchers exist.
     */
    public function highestTopNotcher(): ?array
    {
        $row = DB::table('board_exams')
            ->join('user_profiles', 'board_exams.user_profile_id', '=', 'user_profiles.id')
            ->join('users', 'user_profiles.user_id', '=', 'users.id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->leftJoin('student_course', 'user_profiles.id', '=', 'student_course.user_profile_id')
            ->leftJoin('courses', 'courses.id', '=', 'student_course.course_id')
            ->leftJoin('departments', 'departments.id', '=', 'courses.department_id')
            ->where('roles.name', '=', 'alumni')
            ->where('user_profiles.is_approved', true)
            ->where('users.is_active', true)
            ->where('board_exams.is_top_notcher', true)
            ->where('board_exams.is_verified', true)
            ->whereNotNull('board_exams.top_notcher_rank')
            ->when($this->departmentId, fn ($q) => $q->where('departments.id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId))
            ->select(
                'users.name as user_name',
                'board_exams.top_notcher_rank',
                'board_exams.rate',
                'courses.course_title',
                'departments.dept_name',
                'departments.dept_code',
            )
            ->orderBy('board_exams.top_notcher_rank')
            ->orderByDesc('board_exams.rate')
            ->orderByDesc('board_exams.date_taken')
            ->first();

        if (! $row) {
            return null;
        }

        return [
            'name'   => $row->user_name,
            'rank'   => (int) $row->top_notcher_rank,
            'rate'   => (float) $row->rate,
            'course' => $row->course_title,
            'dept'   => $row->dept_name,
            'dept_code' => $row->dept_code,
        ];
    }

    // =========================================================
    //  BOARD PASSERS — dept → course drill-down
    // =========================================================

    /**
     * Verified passing board-exam count, grouped by department → course.
     * Counts DISTINCT alumni per bucket (a retaker who passed twice still
     * counts once). Only board-type courses, verified attempts, active
     * and approved alumni.
     */
    public function boardPassersByDeptAndCourse(): array
    {
        $q = DB::table('departments')
            ->join('courses', 'courses.department_id', '=', 'departments.id')
            ->join('student_course', 'courses.id', '=', 'student_course.course_id')
            ->join('user_profiles', 'student_course.user_profile_id', '=', 'user_profiles.id')
            ->join('board_exams', 'board_exams.user_profile_id', '=', 'user_profiles.id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'user_profiles.user_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', '=', 'alumni')
            ->where('user_profiles.is_approved', true)
            ->where('board_exams.is_verified', true)
            ->where('board_exams.passed', true)
            ->where('courses.course_type', 'board')
            ->where('departments.is_active', true)
            ->where('courses.is_active', true)
            ->when($this->departmentId, fn ($q) => $q->where('departments.id', $this->departmentId))
            ->when($this->batchId, fn ($q) => $q->where('user_profiles.batch_id', $this->batchId));

        $this->requireActiveUser($q);

        $rows = $q->select(
                'departments.id as dept_id',
                'departments.dept_code',
                'departments.dept_name',
                'courses.id as course_id',
                'courses.course_code',
                'courses.course_title',
                DB::raw('COUNT(DISTINCT user_profiles.user_id) as total'),
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
            $deptKey   = $row->dept_code ?: "DEPT-{$row->dept_id}";
            $courseKey = $row->course_code ?: "COURSE-{$row->course_id}";
            $count     = (int) $row->total;

            if (! isset($result[$deptKey])) {
                $result[$deptKey] = [
                    'name'    => $row->dept_name,
                    'total'   => 0,
                    'courses' => [],
                ];
            }

            $result[$deptKey]['courses'][$courseKey] = [
                'name'  => $row->course_title,
                'total' => $count,
            ];

            $result[$deptKey]['total'] += $count;
        }

        // Sort departments by passers DESC (biggest first).
        uasort($result, fn ($a, $b) => $b['total'] <=> $a['total']);

        return $result;
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