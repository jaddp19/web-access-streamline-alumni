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
    public bool $selectAllOnPage = false;

    protected int $perPage = 10;

    public function updatingPage(): void
    {
        $this->clearSelection();
    }

    protected function clearSelection(): void
    {
        $this->selectedCompanies  = [];
        $this->selectAllFiltered  = false;
        $this->selectAllOnPage    = false;

        // Invalidate memoized computed props so the next render sees
        // the fresh state (Livewire 3 caches them per-request).
        unset($this->selectedCount);
        unset($this->pageRowIds);
    }

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
        return $this->companies->getCollection()->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    #[Computed]
    public function selectedCount(): int
    {
        return $this->selectAllFiltered
            ? $this->totalCompaniesCount
            : count($this->selectedCompanies);
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
            && empty(array_diff($pageIds, $this->selectedCompanies));

        if ($allOnPageSelected && ! $this->selectAllFiltered) {
            $this->selectedCompanies = array_values(
                array_diff($this->selectedCompanies, $pageIds)
            );
        } else {
            $this->selectedCompanies = array_values(array_unique(
                array_merge($this->selectedCompanies, $pageIds)
            ));
        }

        $this->recomputeSelectAllOnPage();

        // Count changed → drop the memoized value so the UI updates.
        unset($this->selectedCount);
    }

    public function toggleRowSelection(int $id): void
    {
        if ($this->selectAllFiltered) {
            $this->selectAllFiltered = false;
            $this->selectedCompanies = $this->pageRowIds;
        }

        if (in_array($id, $this->selectedCompanies, true)) {
            $this->selectedCompanies = array_values(
                array_diff($this->selectedCompanies, [$id])
            );
        } else {
            $this->selectedCompanies[] = $id;
        }

        $this->recomputeSelectAllOnPage();

        // Count changed → drop the memoized value so the UI updates.
        unset($this->selectedCount);
    }

    public function isRowSelected(int $id): bool
    {
        if ($this->selectAllFiltered) {
            return true;
        }
        return in_array($id, $this->selectedCompanies, true);
    }

    protected function recomputeSelectAllOnPage(): void
    {
        $pageIds = $this->pageRowIds;

        $this->selectAllOnPage = ! empty($pageIds)
            && empty(array_diff($pageIds, $this->selectedCompanies));
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

        // Reset internal state.
        $this->selectedCompanies = [];
        $this->selectAllFiltered = false;
        $this->selectAllOnPage   = false;

        // Invalidate memoized computed props so the re-render:
        //   • hides the "N company(ies) selected" bar (selectedCount → 0)
        //   • shows the fresh list (companies recomputed from DB)
        unset($this->selectedCount);
        unset($this->totalCompaniesCount);
        unset($this->pageRowIds);
        unset($this->companies);

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
        $this->recomputeSelectAllOnPage();

        // The single row is gone — invalidate memoized computeds.
        unset($this->selectedCount);
        unset($this->totalCompaniesCount);
        unset($this->pageRowIds);
        unset($this->companies);

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

        // One filesystem call — delete() returns false if missing.
        Storage::disk('public')->delete($relative);
    }
};