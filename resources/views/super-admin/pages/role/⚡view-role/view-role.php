<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    use WithPagination;

    public array $selectedRoles = [];

    /** True when "select every role across all pages" mode is active. */
    public bool $selectAllFiltered = false;

    // --- ADDED FOR VIEW MODAL ---
    public ?int $viewingRoleId = null;

    public function openView(int $id): void
    {
        $this->viewingRoleId = $id;
    }

    public function closeView(): void
    {
        $this->viewingRoleId = null;
    }

    #[Computed]
    public function viewingRole(): ?Role
    {
        if (! $this->viewingRoleId) return null;
        
        return Role::with('permissions')->find($this->viewingRoleId);
    }
    // ----------------------------

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function roles()
    {
        return Role::with('permissions')
            ->select('id', 'name', 'created_at')
            ->latest()
            ->paginate(5);
    }

    #[Computed]
    public function totalRolesCount(): int
    {
        return Role::count();
    }

    #[Computed]
    public function pageRoleIds(): array
    {
        return $this->roles->getCollection()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    // =========================================================
    //  PAGE CHANGE
    // =========================================================

    public function updatedPage(): void
    {
        unset($this->pageRoleIds);
    }

    // =========================================================
    //  SELECTION
    // =========================================================

    public function toggleSelectAll(): void
    {
        if ($this->selectAllFiltered) {
            $this->selectedRoles = [];
            $this->selectAllFiltered = false;

            return;
        }

        $this->selectedRoles = Role::query()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $this->selectAllFiltered = true;
    }

    public function toggleRowSelection($roleId): void
    {
        $roleId = (int) $roleId;

        if ($this->selectAllFiltered) {
            $this->selectAllFiltered = false;
            $this->selectedRoles = Role::query()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if (in_array($roleId, $this->selectedRoles, true)) {
            $this->selectedRoles = array_values(
                array_diff($this->selectedRoles, [$roleId])
            );
        } else {
            $this->selectedRoles[] = $roleId;
        }
    }

    public function isRowSelected(int $id): bool
    {
        return in_array($id, $this->selectedRoles, true);
    }

    // =========================================================
    //  ACTIONS
    // =========================================================

    public function deleteSelected(): void
    {
        if (empty($this->selectedRoles)) {
            session()->flash('error', 'Nothing is selected.');

            return;
        }

        $protected = ['super-admin', 'super admin', 'admin'];

        $deletable = Role::whereIn('id', $this->selectedRoles)
            ->whereNotIn('name', $protected)
            ->pluck('id')
            ->all();

        if (empty($deletable)) {
            session()->flash('error', 'No deletable roles in selection.');

            return;
        }

        Role::whereIn('id', $deletable)->delete();

        $this->selectedRoles = [];
        $this->selectAllFiltered = false;
        $this->resetPage();

        session()->flash('success', count($deletable).' role(s) deleted successfully.');
    }
};