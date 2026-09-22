<?php

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public array $selectedEmails = [];
    public bool $selectAllFiltered = false;
    public bool $selectAllOnPage = false;

    protected int $perPage = 10;

    public function updatingPage(): void
    {
        $this->clearSelection();
    }

    protected function clearSelection(): void
    {
        $this->selectedEmails     = [];
        $this->selectAllFiltered  = false;
        $this->selectAllOnPage    = false;
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function emails()
    {
        return EmailTemplate::query()
            ->select('id', 'template', 'created_at')
            ->latest()
            ->paginate($this->perPage);
    }

    #[Computed]
    public function totalEmailsCount(): int
    {
        return Cache::remember('email-templates:count', now()->addSeconds(30), function () {
            return EmailTemplate::count();
        });
    }

    #[Computed]
    public function pageRowIds(): array
    {
        return $this->emails->getCollection()->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    #[Computed]
    public function selectedCount(): int
    {
        return $this->selectAllFiltered
            ? $this->totalEmailsCount
            : count($this->selectedEmails);
    }

    // =========================================================
    //  SELECTION
    // =========================================================

    public function toggleSelectAllOnPage(): void
    {
        $pageIds = $this->pageRowIds;

        if (empty($pageIds)) {
            return;
        }

        $allOnPageSelected = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedEmails));

        if ($allOnPageSelected && ! $this->selectAllFiltered) {
            $this->selectedEmails = array_values(
                array_diff($this->selectedEmails, $pageIds)
            );
        } else {
            $this->selectedEmails = array_values(array_unique(
                array_merge($this->selectedEmails, $pageIds)
            ));
        }

        $this->recomputeSelectAllOnPage();
    }

    public function toggleRowSelection(int $id): void
    {
        if ($this->selectAllFiltered) {
            $this->selectAllFiltered = false;
            $this->selectedEmails = $this->pageRowIds;
        }

        if (in_array($id, $this->selectedEmails, true)) {
            $this->selectedEmails = array_values(
                array_diff($this->selectedEmails, [$id])
            );
        } else {
            $this->selectedEmails[] = $id;
        }

        $this->recomputeSelectAllOnPage();
    }

    public function isRowSelected(int $id): bool
    {
        if ($this->selectAllFiltered) {
            return true;
        }
        return in_array($id, $this->selectedEmails, true);
    }

    protected function recomputeSelectAllOnPage(): void
    {
        $pageIds = $this->pageRowIds;

        $this->selectAllOnPage = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedEmails));
    }

    // =========================================================
    //  ACTIONS
    // =========================================================

    public function deleteSelected(): void
    {
        abort_unless(auth()->user()?->can('manage-emails'), 403);

        if ($this->selectedCount <= 0) {
            session()->flash('error', 'Nothing is selected.');
            return;
        }

        try {
            $count = DB::transaction(function () {
                $query = EmailTemplate::query();

                if (! $this->selectAllFiltered) {
                    $query->whereIn('id', $this->selectedEmails);
                }

                return $query->delete();
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Delete failed: ' . $e->getMessage());
            return;
        }

        Cache::forget('email-templates:count');

        $this->clearSelection();
        $this->resetPage();

        session()->flash('success', "{$count} email template(s) deleted successfully.");
    }
};