<?php

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $actionFilter = 'all';

    #[Url]
    public string $typeFilter = 'all';

    #[Url]
    public string $userFilter = 'all';

    #[Url]
    public string $dateRange = '30';

    public ?int $viewingLogId = null;

    protected int $perPage = 20;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingUserFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateRange(): void
    {
        $this->resetPage();
    }

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
                $q->where('auditable_type', 'App\\Models\\'.$this->typeFilter);
            })
            ->when($this->userFilter !== 'all', fn ($q) => $q->where('user_id', $this->userFilter))
            ->when($this->dateRange !== 'all', function ($q) {
                $q->where('created_at', '>=', now()->subDays((int) $this->dateRange));
            })
            ->when($this->search !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                // Search indexed/smaller columns only — type has its own filter.
                $q->where(function ($inner) use ($term) {
                    $inner->where('user_name', 'like', $term)
                        ->orWhere('auditable_id', 'like', $term);
                });
            })
            ->latest('created_at')
            ->paginate($this->perPage);
    }

    public function readableChanges(array $changes): array
    {
        $labels = [
            'title' => 'Title',
            'description' => 'Description',
            'location' => 'Location',
            'capacity' => 'Capacity',
            'starts_at' => 'Starts',
            'ends_at' => 'Ends',
            'status' => 'Status',
            'image' => 'Image',
            'category_id' => 'Category',
            'attachments' => 'Attachments',
        ];

        // Noise the coordinator doesn't need to see.
        $skip = ['created_at', 'updated_at', 'deleted_at', 'slug', 'user_id', 'created_by'];

        $rows = [];

        foreach ($changes as $field => $change) {
            if (in_array($field, $skip, true)) {
                continue;
            }

            $isPair = is_array($change)
                && (array_key_exists('old', $change) || array_key_exists('new', $change));

            $old = $isPair ? ($change['old'] ?? null) : null;
            $new = $isPair ? ($change['new'] ?? null) : $change;

            $kind = match ($field) {
                'status' => 'badge',
                'image' => 'image',
                'attachments' => 'files',
                default => 'text',
            };

            $rows[] = [
                'label' => $labels[$field] ?? Str::headline((string) $field),
                'kind' => $kind,
                'hasOld' => $isPair,
                'old' => $this->formatAuditValue($field, $old, $kind),
                'new' => $this->formatAuditValue($field, $new, $kind),
                'changed' => $isPair ? $old !== $new : true,
            ];
        }

        return $rows;
    }

    protected function formatAuditValue(string $field, mixed $value, string $kind): mixed
    {
        if ($field === 'capacity' && ($value === null || $value === '')) {
            return 'Unlimited';
        }

        if ($value === null || $value === '' || $value === []) {
            return null;
        }

        if ($kind === 'image') {
            return filter_var($value, FILTER_VALIDATE_URL) ? $value : Storage::url($value);
        }

        if ($kind === 'files') {
            $list = is_string($value) ? (json_decode($value, true) ?: [$value]) : (array) $value;

            return array_values(array_map(fn ($f) => basename((string) $f), $list));
        }

        if (in_array($field, ['starts_at', 'ends_at'], true)) {
            try {
                return Carbon::parse($value)->format('M j, Y g:i A');
            } catch (Throwable) {
                return (string) $value;
            }
        }

        if ($field === 'category_id') {
            return (new Post)->category()->getRelated()->find($value)?->cat_name
                ?? "Category #{$value}";
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return (string) $value;
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

    public function viewLog(int $id): void
    {
        $this->viewingLogId = $id;
    }

    public function closeLog(): void
    {
        $this->viewingLogId = null;
    }

    public function emailPreviewHtml(?string $subject, ?string $message): string
    {
        try {
            return view('emails.template', [
                'emailSubject' => filled($subject) ? $subject : '(no subject)',
                'bodyHtml' => filled($message) ? $message : '(empty message)',
            ])->render();
        } catch (Throwable $e) {
            report($e);

            return '<div style="padding:24px;font-family:system-ui,sans-serif;color:#b91c1c;">Preview unavailable.</div>';
        }
    }

    #[Computed]
    public function viewingLog(): ?AuditLog
    {
        if (! $this->viewingLogId) {
            return null;
        }

        return AuditLog::find($this->viewingLogId);
    }

    #[Computed]
    public function currentModel(): ?Model
    {
        $log = $this->viewingLog;

        if (! $log || ! $log->auditable_type || ! class_exists($log->auditable_type)) {
            return null;
        }

        if ($log->action === 'deleted') {
            return null;
        }

        return $log->auditable_type::find($log->auditable_id);
    }
};
