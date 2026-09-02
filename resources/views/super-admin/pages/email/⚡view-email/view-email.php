<?php

use App\Models\EmailTemplate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public $selectedEmails = [];
    public $selectAll = false;

    /**
     * Delete all selected emails.
     * Resets selection after deletion.
     */
    public function deleteSelected()
    {
        EmailTemplate::whereIn('id', $this->selectedEmails)->delete();

        $this->selectedEmails = [];
        $this->selectAll = false;

        session()->flash('success', 'Selected email templates deleted successfully.');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedEmails = $this->emails->getCollection()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        } else {
            $this->selectedEmails = [];
        }
    }

    public function updatedSelectedEmails()
    {
        $this->selectAll = count($this->selectedEmails) === $this->totalEmailsCount();
    }

    /**
     * Toggle selection of all emails across pages.
     */
    public function toggleSelectAll()
    {
        if (count($this->selectedEmails) === $this->totalEmailsCount()) {
            $this->selectedEmails = [];
            $this->selectAll = false;
        } else {
            $this->selectedEmails = EmailTemplate::pluck('id')->map(fn ($id) => (int) $id)->toArray();
            $this->selectAll = true;
        }
    }

    /**
     * Toggle selection of a single email.
     */
    public function toggleRowSelection($emailId)
    {
        if (in_array($emailId, $this->selectedEmails)) {
            $this->selectedEmails = array_values(array_diff($this->selectedEmails, [$emailId]));
        } else {
            $this->selectedEmails[] = $emailId;
        }

        $this->selectAll = count($this->selectedEmails) === $this->totalEmailsCount();
    }

    /**
     * Computed property: total number of emails.
     */
    #[Computed]
    public function totalEmailsCount()
    {
        return EmailTemplate::count();
    }

    /**
     * Computed property: paginated email templates.
     * Only the `template` JSON column is fetched — no assumptions
     * are made about its internal structure.
     */
    #[Computed]
    public function emails()
    {
        return EmailTemplate::select('id', 'template', 'created_at')
            ->latest()
            ->paginate(5);
    }
};
