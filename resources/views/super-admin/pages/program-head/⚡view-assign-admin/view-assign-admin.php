<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public $selectedProgramHeads = []; // keys formatted as "{deptId}-{userId}"
    public $selectAll = false;
    public int $page = 1;
    protected int $perPage = 5;

    public function deleteSelected()
    {
        foreach ($this->selectedProgramHeads as $key) {
            [$deptId, $userId] = explode('-', $key, 2);

            $department = Department::find($deptId);

            if ($department && $department->program_head_id == $userId) {
                $department->program_head_id = null; // clear assignment
                $department->save();
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
        return Department::with('programHead')
            ->whereNotNull('program_head_id')
            ->get()
            ->map(fn ($dept) => (object) [
                'key'        => $dept->id . '-' . $dept->program_head_id,
                'department' => $dept,
                'user'       => $dept->programHead,
                'created_at' => $dept->updated_at,
            ])
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
