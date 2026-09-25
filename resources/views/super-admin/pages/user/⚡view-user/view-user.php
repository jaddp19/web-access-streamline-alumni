<?php

use App\Models\Batch;
use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $roleFilter = 'all';

    #[Url]
    public string $search = '';

    #[Url]
    public string $departmentFilter = '';

    #[Url]
    public string $courseFilter = '';

    #[Url]
    public string $batchFilter = '';

    public array $selectedUsers = [];
    public bool $selectAll = false;
    public bool $selectAllFiltered = false;

    /** Memoized filtered query for the current request. */
    protected $filteredQueryCache = null;

    // =========================================================
    //  FILTER UPDATES
    // =========================================================

    public function updatedRoleFilter(): void
    {
        $this->invalidateFilterCache();
    }

    public function updatedSearch(): void
    {
        $this->invalidateFilterCache();
    }

    public function updatedDepartmentFilter(): void
    {
        // If the currently selected course doesn't belong to the new
        // department, drop it so we never end up in an impossible state
        // (department X, course from department Y).
        if ($this->courseFilter !== '' && $this->departmentFilter !== '') {
            $belongs = Course::where('id', (int) $this->courseFilter)
                ->where('department_id', (int) $this->departmentFilter)
                ->exists();

            if (! $belongs) {
                $this->courseFilter = '';
            }
        }

        $this->invalidateFilterCache();
    }

    public function updatedCourseFilter(): void
    {
        $this->invalidateFilterCache();
    }

    public function updatedBatchFilter(): void
    {
        $this->invalidateFilterCache();
    }

    public function setRoleFilter(string $role): void
    {
        $this->roleFilter = $role;

        // Course / department / batch filters only make sense on alumni views.
        if (! in_array($role, ['all', 'alumni'], true)) {
            $this->courseFilter = '';
            $this->departmentFilter = '';
            $this->batchFilter = '';
        }

        $this->invalidateFilterCache();
    }

    public function clearCourseFilters(): void
    {
        $this->courseFilter = '';
        $this->departmentFilter = '';
        $this->batchFilter = '';
        $this->invalidateFilterCache();
    }

    protected function invalidateFilterCache(): void
    {
        $this->filteredQueryCache = null;
        unset($this->totalUsersCount);

        $this->resetPage();

        $this->selectedUsers = [];
        $this->selectAll = false;
        $this->selectAllFiltered = false;
    }

    // =========================================================
    //  QUERY BUILDERS
    // =========================================================

    protected function filteredQuery()
    {
        if ($this->filteredQueryCache !== null) {
            return $this->filteredQueryCache;
        }

        return $this->filteredQueryCache = User::role(['alumni', 'registrar', 'program head'])
            ->when($this->roleFilter !== 'all', fn ($q) => $q->role($this->roleFilter))
            ->when($this->search !== '', function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhere('school_id', 'like', "%{$this->search}%")
                      ->orWhereHas('roles', fn ($r) => $r->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->departmentFilter !== '', function ($q) {
                $q->whereHas('userProfile.courses', function ($c) {
                    $c->where('courses.department_id', (int) $this->departmentFilter);
                });
            })
            ->when($this->courseFilter !== '', function ($q) {
                $q->whereHas('userProfile.courses', function ($c) {
                    $c->where('courses.id', (int) $this->courseFilter);
                });
            })
            ->when($this->batchFilter !== '', function ($q) {
                $q->whereHas('userProfile', function ($p) {
                    $p->where('batch_id', (int) $this->batchFilter);
                });
            });
    }

    protected function selectedUsersQuery()
    {
        if ($this->selectAllFiltered) {
            return $this->filteredQuery();
        }

        return User::role(['alumni', 'registrar', 'program head'])
            ->whereIn('id', $this->selectedUsers);
    }

    // =========================================================
    //  FILTER OPTIONS
    // =========================================================

    #[Computed]
    public function departmentsList()
    {
        return Department::query()
            ->where('is_active', true)
            ->orderBy('dept_name')
            ->get(['id', 'dept_name', 'dept_code']);
    }

    #[Computed]
    public function coursesList()
    {
        return Course::query()
            ->where('is_active', true)
            ->when($this->departmentFilter !== '', fn ($q) => $q->where('department_id', (int) $this->departmentFilter))
            ->orderBy('course_title')
            ->get(['id', 'course_title', 'course_code', 'department_id']);
    }

    /**
     * Batches that actually contain alumni in the current scope.
     * If a department filter is active, only batches with alumni in that dept.
     */
    #[Computed]
    public function batchesList()
    {
        $deptId = $this->departmentFilter !== '' ? (int) $this->departmentFilter : null;

        return Batch::query()
            ->whereHas('userProfiles', function ($p) use ($deptId) {
                $p->whereHas('user', fn ($u) => $u->role('alumni'));

                if ($deptId !== null) {
                    $p->whereHas('courses', fn ($c) => $c->where('department_id', $deptId));
                }
            })
            ->orderByDesc('batch_name')
            ->get(['id', 'batch_name']);
    }

    // =========================================================
    //  SHARED EXPORT EAGER-LOAD
    // =========================================================

    protected function exportQuery()
    {
        return $this->filteredQuery()
            ->with([
                'roles:id,name',
                'tracerStudy:id,user_id',
                'userProfile:id,user_id,avatar,batch_id',
                'userProfile.batch:id,batch_name',
                'userProfile.courses:id,course_title,course_code,department_id',
                'userProfile.courses.department:id,dept_name,dept_code',
            ])
            ->select('id', 'name', 'email', 'school_id', 'created_at')
            ->latest();
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed(persist: true)]
    public function totalUsersCount(): int
    {
        return $this->filteredQuery()->count();
    }

    #[Computed]
    public function users()
    {
        return $this->filteredQuery()
            ->with([
                'roles:id,name',
                'tracerStudy:id,user_id',
                'userProfile:id,user_id,avatar',
            ])
            ->select('id', 'name', 'email', 'created_at')
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function pageUserIds(): array
    {
        return $this->users
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    // =========================================================
    //  SELECTION
    // =========================================================

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectAllFiltered = true;
            $this->selectedUsers = $this->filteredQuery()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        } else {
            $this->selectAllFiltered = false;
            $this->selectedUsers = [];
        }
    }

    public function updatedSelectedUsers(): void
    {
        $pageIds = $this->pageUserIds;

        $this->selectAll = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedUsers));

        $this->selectAllFiltered = false;
    }

    public function toggleSelectAll(): void
    {
        $pageIds = $this->pageUserIds;

        $allOnPageSelected = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedUsers));

        if ($allOnPageSelected) {
            $this->selectedUsers = [];
            $this->selectAll = false;
            $this->selectAllFiltered = false;
        } else {
            $this->selectedUsers = $this->filteredQuery()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();

            $this->selectAll = true;
            $this->selectAllFiltered = true;
        }
    }

    public function toggleRowSelection($userId): void
    {
        $userId = (int) $userId;

        if (in_array($userId, $this->selectedUsers, true)) {
            $this->selectedUsers = array_values(array_diff($this->selectedUsers, [$userId]));
        } else {
            $this->selectedUsers[] = $userId;
        }

        $pageIds = $this->pageUserIds;

        $this->selectAll = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedUsers));

        $this->selectAllFiltered = false;
    }

    // =========================================================
    //  ACTIONS
    // =========================================================

    public function deleteSelected(): void
    {
        $count = $this->selectedUsersQuery()->delete();

        $this->selectedUsers = [];
        $this->selectAll = false;
        $this->selectAllFiltered = false;

        session()->flash('success', "{$count} user(s) deleted successfully.");
    }

    protected function tracerStatusFor(User $user): ?string
    {
        if (! $user->hasRole('alumni')) {
            return null;
        }

        return $user->tracerStudy ? 'Completed' : 'Pending';
    }

    // =========================================================
    //  EXPORTS
    // =========================================================

    public function exportFilteredCsv(): StreamedResponse
    {
        $users = $this->exportQuery()->get();

        return $this->streamUsersAsCsv($users, 'users-filtered');
    }

    public function exportSelectedCsv(): StreamedResponse
    {
        $users = $this->selectedUsersQuery()
            ->with([
                'roles:id,name',
                'tracerStudy:id,user_id',
                'userProfile:id,user_id,avatar,batch_id',
                'userProfile.batch:id,batch_name',
                'userProfile.courses:id,course_title,course_code,department_id',
                'userProfile.courses.department:id,dept_name,dept_code',
            ])
            ->select('id', 'name', 'email', 'school_id', 'created_at')
            ->latest()
            ->get();

        return $this->streamUsersAsCsv($users, 'users-selected');
    }

    protected function streamUsersAsCsv($users, string $filenamePrefix): StreamedResponse
    {
        $filename = $filenamePrefix . '-' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($users) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Name',
                'Email',
                'School ID',
                'Roles',
                'Course(s)',
                'Department(s)',
                'Batch',
                'Tracer Study',
                'Created At',
            ]);

            foreach ($users as $user) {
                $courses     = $user->userProfile?->courses ?? collect();
                $departments = $courses->pluck('department')->filter()->unique('id');

                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->school_id ?? '',
                    $user->roles->pluck('name')->implode(', '),
                    $courses->pluck('course_title')->filter()->join(', ') ?: 'N/A',
                    $departments->pluck('dept_name')->filter()->join(', ') ?: 'N/A',
                    $user->userProfile?->batch?->batch_name ?? 'N/A',
                    $this->tracerStatusFor($user) ?? 'N/A',
                    $user->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
};