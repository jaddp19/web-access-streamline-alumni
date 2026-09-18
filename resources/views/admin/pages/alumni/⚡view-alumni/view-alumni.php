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

new #[Layout('layouts::app-admin')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Department IDs the current user is allowed to see.
     *   - Registrar  → []  (means no restriction)
     *   - Program head → array of department IDs
     *   - Others → [] (but blocked by the hasAccess guard below)
     */
    protected function departmentIds(): array
    {
        $user = Auth::user();
        if (! $user) return [];

        if ($user->hasRole('registrar')) {
            return [];
        }

        return Department::where('program_head_id', $user->id)
            ->pluck('id')
            ->toArray();
    }

    protected function hasAccess(): bool
    {
        $user = Auth::user();
        if (! $user) return false;

        if ($user->hasRole('registrar')) return true;
        if ($user->hasRole('program head')) return ! empty($this->departmentIds());

        return false;
    }

    protected function filteredQuery()
    {
        $user = Auth::user();

        if (! $this->hasAccess()) {
            return UserProfile::query()->whereRaw('0=1');
        }

        $query = UserProfile::query()
            ->whereHas('user', fn ($q) => $q->role('alumni'));

        // Department scoping for program heads
        if ($user->hasRole('program head')) {
            $deptIds = $this->departmentIds();
            $query->whereHas('courses', fn ($q) => $q->whereIn('department_id', $deptIds));
        }

        // Search — name, email, school_id, batch, course title, course code
        if ($this->search !== '') {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('user', function ($u) use ($term) {
                        $u->where('name', 'like', $term)
                          ->orWhere('email', 'like', $term)
                          ->orWhere('school_id', 'like', $term);
                    })
                    ->orWhereHas('batch', fn ($b) => $b->where('batch_name', 'like', $term))
                    ->orWhereHas('courses', function ($c) use ($term) {
                        $c->where('course_title', 'like', $term)
                          ->orWhere('course_code', 'like', $term);
                    });
            });
        }

        return $query;
    }

    #[Computed]
    public function alumni()
    {
        return $this->filteredQuery()
            ->with(['user', 'batch', 'courses.department'])
            ->latest()
            ->paginate(5);
    }

    /**
     * Export the currently filtered result set — respects
     * department scope AND search term. Exports everything matching,
     * not just the current page.
     */
    public function exportCsv(): StreamedResponse
    {
        $profiles = $this->filteredQuery()
            ->with(['user', 'batch', 'courses.department'])
            ->latest()
            ->get();

        $filename = 'alumni-' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($profiles) {
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

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
};