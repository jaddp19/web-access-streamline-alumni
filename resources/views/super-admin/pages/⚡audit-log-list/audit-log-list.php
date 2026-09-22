<?php

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    #[Url] public string $search = '';
    #[Url] public string $actionFilter = 'all';
    #[Url] public string $typeFilter = 'all';
    #[Url] public string $userFilter = 'all';
    #[Url] public string $dateRange = '30';

    protected int $perPage = 20;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingActionFilter(): void { $this->resetPage(); }
    public function updatingTypeFilter(): void { $this->resetPage(); }
    public function updatingUserFilter(): void { $this->resetPage(); }
    public function updatingDateRange(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->actionFilter = 'all';
        $this->typeFilter = 'all';
        $this->userFilter = 'all';
        $this->dateRange = '30';
        $this->resetPage();
    }

    #[Computed]
    public function logs()
    {
        return AuditLog::query()
            ->with('user:id,name')
            ->when($this->actionFilter !== 'all', fn ($q) => $q->where('action', $this->actionFilter))
            ->when($this->typeFilter !== 'all', function ($q) {
                $q->where('auditable_type', 'App\\Models\\' . $this->typeFilter);
            })
            ->when($this->userFilter !== 'all', fn ($q) => $q->where('user_id', $this->userFilter))
            ->when($this->dateRange !== 'all', function ($q) {
                $q->where('created_at', '>=', now()->subDays((int) $this->dateRange));
            })
            ->when($this->search !== '', function ($q) {
                $term = '%' . trim($this->search) . '%';
                // Search indexed/smaller columns only — type has its own filter.
                $q->where(function ($inner) use ($term) {
                    $inner->where('user_name', 'like', $term)
                          ->orWhere('auditable_id', 'like', $term);
                });
            })
            ->latest('created_at')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function users(): array
    {
        return Cache::remember('audit:users', now()->addMinutes(10), function () {
            $ids = AuditLog::query()
                ->whereNotNull('user_id')
                ->distinct()
                ->pluck('user_id');

            return User::query()
                ->whereIn('id', $ids)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($u) => ['id' => (int) $u->id, 'name' => (string) $u->name])
                ->all();
        });
    }

    #[Computed]
    public function availableTypes(): array
    {
        return Cache::remember('audit:types', now()->addMinutes(10), function () {
            return AuditLog::query()
                ->distinct()
                ->pluck('auditable_type')
                ->map(fn ($t) => class_basename($t))
                ->sort()
                ->values()
                ->all();
        });
    }

    #[Computed]
    public function hasFilters(): bool
    {
        return $this->search !== ''
            || $this->actionFilter !== 'all'
            || $this->typeFilter !== 'all'
            || $this->userFilter !== 'all'
            || $this->dateRange !== '30';
    }
};