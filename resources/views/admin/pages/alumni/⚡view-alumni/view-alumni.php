<?php

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

    protected int $perPage = 15;

    /** Memoized filtered query for the current request. */
    protected $filteredQueryCache = null;

    public function updatingSearch(): void
    {
        $this->filteredQueryCache = null;
        $this->resetPage();
    }

    // =========================================================
    //  ACCESS + SCOPE
    // =========================================================

    /**
     * Department IDs the current user is allowed to see.
     * Registrar → [] (global) · Program head → [ids] · No dept → []
     */
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
            ->select('id', 'user_id', 'avatar', 'batch_id', 'created_at')
            ->whereHas('user', fn ($q) => $q->role('alumni'));

        if ($this->isProgramHead && ! empty($this->departmentIds)) {
            $deptIds = $this->departmentIds;
            $query->whereHas('courses', fn ($q) => $q->whereIn('department_id', $deptIds));
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
                'courses:id,course_title,course_code',
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

        // Capture the query now — the callback runs after the response is queued.
        $query = $this->filteredQuery()
            ->with([
                'user:id,name,email,school_id',
                'batch:id,batch_name',
                'courses:id,course_title,course_code',
                'courses.department:id,dept_name',
            ])
            ->latest('id');

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Name',
                'Email',
                'School ID',
                'Course(s)',
                'Department(s)',
                'Batch',
                'Registered',
            ]);

            // Stream in chunks — avoids loading thousands of profiles into memory.
            $query->chunk(500, function ($profiles) use ($handle) {
                foreach ($profiles as $profile) {
                    fputcsv($handle, [
                        $profile->user?->name ?? 'N/A',
                        $profile->user?->email ?? 'N/A',
                        $profile->user?->school_id ?? '',
                        $profile->courses->pluck('course_title')->filter()->join(', ') ?: 'N/A',
                        $profile->courses->pluck('department.dept_name')->filter()->unique()->join(', ') ?: 'N/A',
                        $profile->batch?->batch_name ?? 'N/A',
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