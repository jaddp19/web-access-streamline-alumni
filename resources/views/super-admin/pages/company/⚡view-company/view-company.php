<?php

use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public array $selectedCompanies = [];
    public bool $selectAll = false;

    #[Computed]
    public function totalCompaniesCount()
    {
        return Company::count();
    }

    #[Computed]
    public function companies()
    {
        return Company::select('id', 'company_name', 'company_logo', 'company_address', 'company_desc', 'created_at')
            ->orderBy('company_name', 'asc')
            ->paginate(5);
    }

    public function toggleRowSelection(int $id): void
    {
        if (in_array($id, $this->selectedCompanies, true)) {
            $this->selectedCompanies = array_values(
                array_diff($this->selectedCompanies, [$id])
            );
        } else {
            $this->selectedCompanies[] = $id;
        }
    }

    public function toggleSelectAll(): void
    {
        $this->selectAll = ! $this->selectAll;

        $this->selectedCompanies = $this->selectAll
            ? Company::pluck('id')->map(fn ($id) => (int) $id)->toArray()
            : [];
    }

    /**
     * Delete selected companies + their logo files from storage.
     */
    public function deleteSelected(): void
    {
        $companies = Company::whereIn('id', $this->selectedCompanies)->get();

        // Delete logo files first (before DB rows vanish)
        foreach ($companies as $company) {
            $this->deleteLogoFile($company->company_logo, $company->id);
        }

        Company::whereIn('id', $this->selectedCompanies)->delete();

        $this->selectedCompanies = [];
        $this->selectAll = false;

        session()->flash('success', 'Selected companies and their logos deleted successfully.');
    }

    /**
     * Delete a single company + its logo file.
     * (Use from a row-level delete button.)
     */
    public function deleteCompany(int $id): void
    {
        $company = Company::find($id);
        if (! $company) return;

        $this->deleteLogoFile($company->company_logo, $company->id);
        $company->delete();

        // Sync selection
        $this->selectedCompanies = array_values(
            array_diff($this->selectedCompanies, [$id])
        );
        $this->selectAll = count($this->selectedCompanies) === $this->totalCompaniesCount;

        session()->flash('success', "Company \"{$company->company_name}\" deleted.");
    }

    /**
     * Safely delete a company logo file from the public disk.
     *
     * Skips:
     *  - null/empty values
     *  - full external URLs (http/https)
     *  - files still referenced by another company
     */
    protected function deleteLogoFile(?string $path, ?int $exceptId = null): void
    {
        if (blank($path)) return;

        // External URLs — never touch
        if (filter_var($path, FILTER_VALIDATE_URL)) return;

        // Public-folder assets (e.g. /imgs/...) — not in storage, skip
        if (str_starts_with($path, '/imgs/') || str_starts_with($path, '/storage/')) return;

        $relative = ltrim($path, '/');

        // Check if any OTHER company still uses this exact path
        $stillUsed = Company::query()
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->where('company_logo', $path)
            ->exists();

        if ($stillUsed) return;

        // Delete from storage/app/public/{relative}
        if (Storage::disk('public')->exists($relative)) {
            Storage::disk('public')->delete($relative);
        }
    }
};