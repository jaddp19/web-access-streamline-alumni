<?php

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public array $selectedEmails = [];

    public bool $selectAll = false;

    public bool $selectAllFiltered = false;

    protected int $perPage = 10;

    // =========================================================
    //  QUERY BUILDERS
    // =========================================================

    protected function filteredQuery()
    {
        return EmailTemplate::query();
    }

    protected function selectedEmailsQuery()
    {
        if ($this->selectAllFiltered) {
            return $this->filteredQuery();
        }

        return EmailTemplate::query()->whereIn('id', $this->selectedEmails);
    }

    public function updatedPage(): void
    {
        unset($this->pageRowIds);

        // Recompute the header checkbox state for the new page.
        $pageIds = $this->pageRowIds;

        $this->selectAll = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedEmails));
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
        return Cache::remember(
            'email-templates:count',
            now()->addSeconds(30),
            fn () => EmailTemplate::count()
        );
    }

    #[Computed]
    public function pageRowIds(): array
    {
        return $this->emails
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
            $this->selectedEmails = $this->filteredQuery()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        } else {
            $this->selectAllFiltered = false;
            $this->selectedEmails = [];
        }
    }

    public function updatedSelectedEmails(): void
    {
        $pageIds = $this->pageRowIds;

        $this->selectAll = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedEmails));

        $this->selectAllFiltered = false;
    }

    public function toggleSelectAll(): void
    {
        // Everything is already selected → clear it.
        if ($this->selectAllFiltered) {
            $this->selectedEmails = [];
            $this->selectAll = false;
            $this->selectAllFiltered = false;

            return;
        }

        // Otherwise: select EVERY template — across every page,
        // not just the current page.
        $this->selectedEmails = $this->filteredQuery()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $this->selectAll = true;
        $this->selectAllFiltered = true;
    }

    public function toggleRowSelection($emailId): void
    {
        $emailId = (int) $emailId;

        if (in_array($emailId, $this->selectedEmails, true)) {
            $this->selectedEmails = array_values(array_diff($this->selectedEmails, [$emailId]));
        } else {
            $this->selectedEmails[] = $emailId;
        }

        $pageIds = $this->pageRowIds;

        $this->selectAll = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedEmails));

        $this->selectAllFiltered = false;
    }

    // =========================================================
    //  ACTIONS
    // =========================================================

    public function deleteSelected(): void
    {
        $count = $this->selectedEmailsQuery()->delete();

        Cache::forget('email-templates:count');

        $this->selectedEmails = [];
        $this->selectAll = false;
        $this->selectAllFiltered = false;

        unset($this->emails, $this->totalEmailsCount, $this->pageRowIds);

        $this->resetPage();

        session()->flash('success', "{$count} email template(s) deleted successfully.");
    }
};