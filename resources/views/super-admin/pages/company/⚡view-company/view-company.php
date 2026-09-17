<?php

use App\Models\Company;
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

    public function deleteSelected(): void
    {
        Company::whereIn('id', $this->selectedCompanies)->delete();

        $this->selectedCompanies = [];
        $this->selectAll = false;
    }
};