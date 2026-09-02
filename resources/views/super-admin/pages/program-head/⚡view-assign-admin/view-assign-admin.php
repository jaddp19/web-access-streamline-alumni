<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public $selectedProgramHeads = []; // keys formatted as "{roleId}-{userId}"
    public $selectAll = false;
    public int $page = 1;
    protected int $perPage = 5;

    public function deleteSelected()
    {
        foreach ($this->selectedProgramHeads as $key) {
            [$roleId, $userId] = explode('-', $key, 2);

            $role = Role::find($roleId);
            $user = User::find($userId);

            if ($role && $user) {
                $user->removeRole($role);

                if ($role->users()->count() === 0) {
                    $role->delete();
                }
            }
        }

        $this->selectedProgramHeads = [];
        $this->selectAll = false;

        session()->flash('success', 'Selected program head assignment(s) removed successfully.');
    }

    public function updatedSelectAll($value)
    {
        $this->selectedProgramHeads = $value
            ? $this->allProgramHeadRows->pluck('key')->toArray()
            : [];
    }

    public function updatedSelectedProgramHeads()
    {
        $this->selectAll = count($this->selectedProgramHeads) === $this->totalProgramHeadsCount;
    }

    public function toggleSelectAll()
    {
        if (count($this->selectedProgramHeads) === $this->totalProgramHeadsCount) {
            $this->selectedProgramHeads = [];
            $this->selectAll = false;
        } else {
            $this->selectedProgramHeads = $this->allProgramHeadRows->pluck('key')->toArray();
            $this->selectAll = true;
        }
    }

    public function toggleRowSelection($key)
    {
        if (in_array($key, $this->selectedProgramHeads)) {
            $this->selectedProgramHeads = array_values(array_diff($this->selectedProgramHeads, [$key]));
        } else {
            $this->selectedProgramHeads[] = $key;
        }

        $this->selectAll = count($this->selectedProgramHeads) === $this->totalProgramHeadsCount;
    }

    public function previousPage()
    {
        $this->page = max(1, $this->page - 1);
    }

    public function nextPage()
    {
        $this->page++;
    }

    #[Computed]
    public function allProgramHeadRows()
    {
        $departments = Department::select('id', 'dept_name')->get()->keyBy('id');

        return Role::where('name', 'like', 'program-head-%')
            ->with('users:id,name')
            ->get()
            ->flatMap(function ($role) use ($departments) {
                $deptId = (int) Str::after($role->name, 'program-head-');
                $department = $departments->get($deptId);

                return $role->users->map(fn ($user) => (object) [
                    'key'        => $role->id . '-' . $user->id,
                    'role_id'    => $role->id,
                    'user'       => $user,
                    'department' => $department,
                    'created_at' => $role->created_at,
                ]);
            })
            ->sortByDesc('created_at')
            ->values();
    }

    #[Computed]
    public function totalProgramHeadsCount()
    {
        return $this->allProgramHeadRows->count();
    }

    #[Computed]
    public function programHeads()
    {
        $items = $this->allProgramHeadRows;
        $offset = ($this->page - 1) * $this->perPage;

        return new LengthAwarePaginator(
            $items->slice($offset, $this->perPage)->values(),
            $items->count(),
            $this->perPage,
            $this->page,
            ['path' => request()->url()]
        );
    }
};
