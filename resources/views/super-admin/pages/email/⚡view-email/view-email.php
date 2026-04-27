<?php

use App\Models\Email;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app-super-admin')] class extends Component
{
    use WithPagination; // 🔑 enable pagination methods
    public $selectedEmails = []; // @var array $selectedEmails IDs of selected emails across all pages
    public $selectAll = false; // @var bool $selectAll Whether all emails are selected
    
    /**
     * Delete all selected emails.
     * Resets selection after deletion.
     */
    public function deleteSelected()
    {
        Email::whereIn('id', $this->selectedEmails)->delete();

        $this->selectedEmails = [];
        $this->selectAll = false;

        session()->flash('success', 'Selected emails deleted successfully.');
    }

    public function updatedSelectAll($value) 
    { 
        if ($value) 
        { 
            // Grab IDs from the all pages, not just current page
            $this->selectedEmails = $this->emails->getCollection()
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray(); 
        } else { 
            $this->selectedEmails = []; 
        } 
    } 
                     
    public function updatedSelectedEmails() 
    { 
        // Keep header checkbox in sync 
        $this->selectAll = count($this->selectedEmails) === $this->totalEmailsCount(); 
    }

    /**
     * Toggle selection of all emails across pages.
     */
    public function toggleSelectAll()
    {
        $allIds = Email::pluck('id')->map(fn($id) => (int) $id)->toArray();

        $selectedCount = count($this->selectedEmails);
        $totalCount = $this->totalEmailsCount;

        if (count($this->selectedEmails) === $this->totalEmailsCount()) 
            { 
                $this->selectedEmails = []; 
                $this->selectAll = false; 
            } else { 
                $this->selectedEmails = $allIds; $this->selectAll = true; 
            }
    }

    /**
     * Toggle selection of a single email.
     */
    public function toggleRowSelection($emailId)
    {
        if (in_array($emailId, $this->selectedEmails)) {
            // Remove if already selected
            $this->selectedEmails = array_values(array_diff($this->selectedEmails, [$emailId]));
        } else {
            // Add if not selected
            $this->selectedEmails[] = $emailId;
        }

        // Sync header checkbox
        $this->selectAll = count($this->selectedEmails) === $this->totalEmailsCount();
    }
    
    /**
     * Computed property: total number of emails.
     */
    #[Computed]
    public function totalEmailsCount()
    {
        return Email::count();
    }
    
    /**
     * Computed property: paginated emails with roles.
     */
    #[Computed()]
    public function emails()
    {
        
        return Email::select('id', 'slug', 'subject', 'message', 'created_at')
            ->latest()->paginate(5);
    }
};