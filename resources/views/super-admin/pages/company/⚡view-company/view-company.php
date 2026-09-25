<?php

use App\Models\Company;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public array $selectedCompanies = [];
    public bool $selectAllFiltered = false;

    protected int $perPage = 10;

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function companies()
    {
        return Company::query()
            ->select('id', 'company_name', 'company_logo', 'company_address', 'company_desc', 'created_at')
            ->orderBy('company_name', 'asc')
            ->paginate($this->perPage);
    }

    #[Computed]
    public function totalCompaniesCount(): int
    {
        return Cache::remember('companies:count', now()->addSeconds(30), function () {
            return Company::count();
        });
    }

    #[Computed]
    public function pageRowIds(): array
    {
        return $this->companies->getCollection()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    #[Computed]
    public function selectedCount(): int
    {
        return $this->selectAllFiltered
            ? $this->totalCompaniesCount
            : count($this->selectedCompanies);
    }

    // =========================================================
    //  PAGE CHANGE
    // =========================================================

    /** Keep selection across pages — just bust the paginated cache. */
    public function updatedPage(): void
    {
        unset($this->pageRowIds, $this->companies);
    }

    // =========================================================
    //  SELECTION
    // =========================================================

    /** Header checkbox / mobile "Select all" — selects everything across all pages. */
    public function toggleSelectAll(): void
    {
        if ($this->selectAllFiltered) {
            $this->clearSelection();
            return;
        }

        $this->selectedCompanies = Company::query()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $this->selectAllFiltered = true;

        $this->refreshSelection();
    }

    /** Enter "all N" mode without materialising IDs. */
    public function selectAllMatching(): void
    {
        $this->selectAllFiltered = true;
        $this->selectedCompanies = [];

        $this->refreshSelection();
    }

    /** Reset everything. Public so Blade can wire a "Clear selection" link. */
    public function clearSelection(): void
    {
        $this->selectedCompanies = [];
        $this->selectAllFiltered = false;

        $this->refreshSelection();
    }

    /** Individual row toggle. */
    public function toggleRowSelection($companyId): void
    {
        $companyId = (int) $companyId;

        // Leaving "all" mode: materialise the full set so unchecking one
        // row leaves everything else selected.
        if ($this->selectAllFiltered) {
            $this->selectAllFiltered = false;
            $this->selectedCompanies = Company::query()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if (in_array($companyId, $this->selectedCompanies, true)) {
            $this->selectedCompanies = array_values(
                array_diff($this->selectedCompanies, [$companyId])
            );
        } else {
            $this->selectedCompanies[] = $companyId;
        }

        $this->refreshSelection();
    }

    public function isRowSelected(int $id): bool
    {
        return $this->selectAllFiltered
            || in_array($id, $this->selectedCompanies, true);
    }

    protected function refreshSelection(): void
    {
        unset(
            $this->selectedCount,
            $this->pageRowIds,
        );
    }

    // =========================================================
    //  ACTIONS
    // =========================================================

    public function deleteSelected(): void
    {
        if ($this->selectedCount <= 0) {
            session()->flash('error', 'Nothing is selected.');
            return;
        }

        try {
            $result = DB::transaction(function () {
                $query = Company::query();

                if (! $this->selectAllFiltered) {
                    if (empty($this->selectedCompanies)) {
                        return ['count' => 0, 'logos' => []];
                    }
                    $query->whereIn('id', $this->selectedCompanies);
                }

                // Capture logos + ids BEFORE delete for post-commit file cleanup.
                $logos = (clone $query)->pluck('company_logo', 'id')->all();
                $count = $query->delete();

                return ['count' => $count, 'logos' => $logos];
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Delete failed: ' . $e->getMessage());
            return;
        }

        // Delete logo files AFTER successful commit.
        foreach ($result['logos'] as $companyId => $logo) {
            $this->deleteLogoFile($logo, (int) $companyId);
        }

        Cache::forget('companies:count');

        $this->clearSelection();
        $this->resetPage();

        session()->flash('success', "{$result['count']} company(ies) deleted successfully.");
    }

    public function deleteCompany(int $id): void
    {
        abort_unless(auth()->user()?->can('manage-companies'), 403);

        $company = Company::find($id);

        if (! $company) {
            session()->flash('error', 'Company not found.');
            return;
        }

        $name = $company->company_name;
        $logo = $company->company_logo;

        try {
            DB::transaction(fn () => $company->delete());
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Delete failed. Please try again.');
            return;
        }

        // File cleanup after commit.
        $this->deleteLogoFile($logo, $id);

        Cache::forget('companies:count');

        $this->selectedCompanies = array_values(
            array_diff($this->selectedCompanies, [$id])
        );

        $this->refreshSelection();

        session()->flash('success', "Company \"{$name}\" deleted.");
    }

    /**
     * Safely delete a company logo from the public disk.
     * Skips: null, external URLs, /imgs/, /storage/, and shared paths.
     */
    protected function deleteLogoFile(?string $path, ?int $exceptId = null): void
    {
        if (blank($path)) return;

        if (filter_var($path, FILTER_VALIDATE_URL)) return;
        if (str_starts_with($path, '/imgs/') || str_starts_with($path, '/storage/')) return;

        $relative = ltrim($path, '/');

        $stillUsed = Company::query()
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->where('company_logo', $path)
            ->exists();

        if ($stillUsed) return;

        Storage::disk('public')->delete($relative);
    }
};