<?php

use App\Models\Batch;
use App\Models\Course;
use App\Models\Department;
use App\Models\UserProfile;
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
    public string $search = '';

    #[Url]
    public string $courseFilter = '';

    #[Url]
    public string $batchFilter = '';

    #[Url]
    public string $statusFilter = '';   // '' | 'approved' | 'pending' | 'rejected'

    protected int $perPage = 10;

    /** Memoized filtered query for the current request. */
    protected $filteredQueryCache = null;

    public function updatingSearch(): void
    {
        $this->filteredQueryCache = null;
        $this->resetPage();
    }

    public function updatingCourseFilter(): void
    {
        $this->filteredQueryCache = null;
        $this->resetPage();
    }

    public function updatingBatchFilter(): void
    {
        $this->filteredQueryCache = null;
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->filteredQueryCache = null;
        $this->resetPage();
    }

    public function clearCourseFilter(): void
    {
        $this->courseFilter = '';
        $this->filteredQueryCache = null;
        $this->resetPage();
    }

    public function clearBatchFilter(): void
    {
        $this->batchFilter = '';
        $this->filteredQueryCache = null;
        $this->resetPage();
    }

    public function clearAllFilters(): void
    {
        $this->search = '';
        $this->courseFilter = '';
        $this->batchFilter = '';
        $this->statusFilter = '';
        $this->filteredQueryCache = null;
        $this->resetPage();
    }

    // =========================================================
    //  ACCESS + SCOPE
    // =========================================================

    #[Computed]
    public function departmentIds(): array
    {
        $user = Auth::user();
        if (! $user) return [];
        if ($user->hasRole('registrar')) return [];

        return Department::where('program_head_id', $user->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    #[Computed]
    public function hasAccess(): bool
    {
        $user = Auth::user();
        if (! $user) return false;
        if ($user->hasRole('registrar')) return true;
        if ($user->hasRole('program head')) return ! empty($this->departmentIds);

        return false;
    }

    #[Computed]
    public function isProgramHead(): bool
    {
        return Auth::user()?->hasRole('program head') ?? false;
    }

    #[Computed]
    public function isRegistrar(): bool
    {
        return Auth::user()?->hasRole('registrar') ?? false;
    }

    #[Computed]
    public function coursesList()
    {
        return Course::query()
            ->where('is_active', true)
            ->when(! empty($this->departmentIds), function ($q) {
                $q->whereIn('department_id', $this->departmentIds);
            })
            ->orderBy('course_title')
            ->get(['id', 'course_title', 'course_code', 'department_id']);
    }

    #[Computed]
    public function batchesList()
    {
        return Batch::query()
            ->whereHas('userProfiles', function ($p) {
                $p->whereHas('user', fn ($u) => $u->role('alumni'));

                if (! empty($this->departmentIds)) {
                    $deptIds = $this->departmentIds;
                    $p->whereHas('courses', fn ($c) => $c->whereIn('department_id', $deptIds));
                }
            })
            ->orderByDesc('batch_name')
            ->get(['id', 'batch_name']);
    }

    // =========================================================
    //  STATUS COUNTS (for filter tab badges)
    // =========================================================

    /**
     * Base query for counting — respects scope + active search/course/batch
     * filters, but ignores statusFilter (so each tab shows its own total).
     */
    protected function countsBaseQuery()
    {
        $q = UserProfile::query()
            ->whereHas('user', fn ($uq) => $uq->role('alumni'));

        if ($this->isProgramHead && ! empty($this->departmentIds)) {
            $deptIds = $this->departmentIds;
            $q->whereHas('courses', fn ($c) => $c->whereIn('department_id', $deptIds));
        }

        if ($this->courseFilter !== '') {
            $q->whereHas('courses', fn ($c) => $c->where('courses.id', (int) $this->courseFilter));
        }

        if ($this->batchFilter !== '') {
            $q->where('batch_id', (int) $this->batchFilter);
        }

        if ($this->search !== '') {
            $term = '%' . trim($this->search) . '%';
            $q->where(function ($q) use ($term) {
                $q->whereHas('user', function ($u) use ($term) {
                    $u->where(function ($inner) use ($term) {
                        $inner->where('name', 'like', $term)
                              ->orWhere('email', 'like', $term)
                              ->orWhere('school_id', 'like', $term);
                    });
                })
                ->orWhereHas('batch', fn ($b) => $b->where('batch_name', 'like', $term))
                ->orWhereHas('courses', function ($c) use ($term) {
                    $c->where('course_title', 'like', $term)
                      ->orWhere('course_code', 'like', $term);
                });
            });
        }

        return $q;
    }

    #[Computed]
    public function statusCounts(): array
    {
        if (! $this->hasAccess) {
            return ['all' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0];
        }

        $base = $this->countsBaseQuery();

        $all      = (clone $base)->count();
        $approved = (clone $base)->where('is_approved', true)->count();
        $rejected = (clone $base)
            ->where('is_approved', false)
            ->whereNotNull('last_rejection_reason')
            ->count();
        $pending  = (clone $base)
            ->where('is_approved', false)
            ->whereNull('last_rejection_reason')
            ->count();

        return [
            'all'      => $all,
            'approved' => $approved,
            'pending'  => $pending,
            'rejected' => $rejected,
        ];
    }

    // =========================================================
    //  QUERY
    // =========================================================

    protected function filteredQuery()
    {
        if ($this->filteredQueryCache !== null) {
            return $this->filteredQueryCache;
        }

        if (! $this->hasAccess) {
            return $this->filteredQueryCache = UserProfile::query()->whereRaw('0 = 1');
        }

        $query = UserProfile::query()
            ->select('id', 'user_id', 'avatar', 'batch_id', 'is_approved', 'last_rejection_reason', 'created_at')
            ->whereHas('user', fn ($q) => $q->role('alumni'));

        if ($this->isProgramHead && ! empty($this->departmentIds)) {
            $deptIds = $this->departmentIds;
            $query->whereHas('courses', fn ($q) => $q->whereIn('department_id', $deptIds));
        }

        // ---- Course filter ----
        if ($this->courseFilter !== '') {
            $courseId = (int) $this->courseFilter;
            $query->whereHas('courses', fn ($c) => $c->where('courses.id', $courseId));
        }

        // ---- Batch filter ----
        if ($this->batchFilter !== '') {
            $batchId = (int) $this->batchFilter;
            $query->where('batch_id', $batchId);
        }

        // ---- Approval status filter ----
        if ($this->statusFilter === 'approved') {
            $query->where('is_approved', true);
        } elseif ($this->statusFilter === 'rejected') {
            $query->where('is_approved', false)->whereNotNull('last_rejection_reason');
        } elseif ($this->statusFilter === 'pending') {
            $query->where('is_approved', false)->whereNull('last_rejection_reason');
        }

        if ($this->search !== '') {
            $term = '%' . trim($this->search) . '%';

            $query->where(function ($q) use ($term) {
                $q->whereHas('user', function ($u) use ($term) {
                    $u->where(function ($inner) use ($term) {
                        $inner->where('name', 'like', $term)
                              ->orWhere('email', 'like', $term)
                              ->orWhere('school_id', 'like', $term);
                    });
                })
                ->orWhereHas('batch', fn ($b) => $b->where('batch_name', 'like', $term))
                ->orWhereHas('courses', function ($c) use ($term) {
                    $c->where('course_title', 'like', $term)
                      ->orWhere('course_code', 'like', $term);
                });
            });
        }

        return $this->filteredQueryCache = $query;
    }

    #[Computed]
    public function alumni()
    {
        return $this->filteredQuery()
            ->with([
                'user:id,name,email,school_id',
                'batch:id,batch_name',
                'courses:id,course_title,course_code,department_id',
                'courses.department:id,dept_name',
            ])
            ->latest('id')
            ->paginate($this->perPage);
    }

    // =========================================================
    //  EXPORT
    // =========================================================

    public function exportCsv(): StreamedResponse
    {
        abort_unless($this->hasAccess, 403);

        $filename = 'alumni-' . now()->format('Y-m-d_His') . '.csv';

        $query = $this->filteredQuery()
            ->with([
                'user:id,first_name,middle_name,last_name,email,school_id',
                'batch:id,batch_name',
                'courses:id,course_title,course_code,department_id',
                'courses.department:id,dept_name',
            ])
            ->latest('id');

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'First Name',
                'Middle Name',
                'Last Name',
                'Email',
                'School ID',
                'Course(s)',
                'Department(s)',
                'Batch',
                'Approval Status',
                'Registered',
            ]);

            $query->chunk(500, function ($profiles) use ($handle) {
                foreach ($profiles as $profile) {
                    fputcsv($handle, [
                        $profile->user?->first_name ?? '',
                        $profile->user?->middle_name ?? '',
                        $profile->user?->last_name ?? '',
                        $profile->user?->email ?? 'N/A',
                        $profile->user?->school_id ?? '',
                        $profile->courses->pluck('course_title')->filter()->join(', ') ?: 'N/A',
                        $profile->courses->pluck('department.dept_name')->filter()->unique()->join(', ') ?: 'N/A',
                        $profile->batch?->batch_name ?? 'N/A',
                        $profile->approvalLabel(),
                        $profile->created_at?->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
};