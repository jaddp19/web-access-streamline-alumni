<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public ?int $user_id = null;
    public ?int $department_id = null;

    /** User confirmed they want to replace the existing department head. */
    public bool $confirmReassign = false;

    public function updatedDepartmentId(): void
    {
        $this->confirmReassign = false;
        $this->resetErrorBag('confirmReassign');
    }

    public function updatedUserId(): void
    {
        $this->confirmReassign = false;
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    /**
     * Active program-head users.
     *
     * Cached as a plain ARRAY of ['id' => int, 'name' => string] —
     * never cache Eloquent collections, they lose their identity across
     * the serialization boundary (this is what caused
     * "Attempt to read property 'id' on string").
     */
    #[Computed]
    public function users(): array
    {
        $cached = Cache::get('assign:program-head-users:v2');

        if (! is_array($cached)) {
            Cache::forget('assign:program-head-users:v2');
            $cached = null;
        }

        if ($cached === null) {
            $cached = User::role('program head')
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($u) => ['id' => (int) $u->id, 'name' => (string) $u->name])
                ->all();

            Cache::put('assign:program-head-users:v2', $cached, now()->addMinutes(5));
        }

        return $cached;
    }

    /**
     * Active departments as plain arrays.
     */
    #[Computed]
    public function departments(): array
    {
        $cached = Cache::get('assign:active-departments:v2');

        if (! is_array($cached)) {
            Cache::forget('assign:active-departments:v2');
            $cached = null;
        }

        if ($cached === null) {
            $cached = Department::query()
                ->where('is_active', true)
                ->orderBy('dept_name')
                ->get(['id', 'dept_name', 'program_head_id'])
                ->map(fn ($d) => [
                    'id'              => (int) $d->id,
                    'dept_name'       => (string) $d->dept_name,
                    'program_head_id' => $d->program_head_id ? (int) $d->program_head_id : null,
                ])
                ->all();

            Cache::put('assign:active-departments:v2', $cached, now()->addMinutes(5));
        }

        return $cached;
    }

    /**
     * Name of the program head currently assigned to the selected department.
     * Returns null if no department is selected, no head is assigned, or the
     * current head is the same user we're about to assign.
     */
    #[Computed]
    public function currentDepartmentHeadName(): ?string
    {
        if (! $this->department_id) {
            return null;
        }

        $department = collect($this->departments)
            ->firstWhere('id', $this->department_id);

        if (! $department || ! $department['program_head_id']) {
            return null;
        }

        // Same user → no warning.
        if ((int) $department['program_head_id'] === (int) $this->user_id) {
            return null;
        }

        // Look up the name — check the users list first (already loaded),
        // fall back to a single DB hit if the head isn't a program-head user anymore.
        $fromList = collect($this->users)
            ->firstWhere('id', $department['program_head_id']);

        if ($fromList) {
            return $fromList['name'];
        }

        return User::query()
            ->whereKey($department['program_head_id'])
            ->value('name');
    }

    #[Computed]
    public function isReassigning(): bool
    {
        return $this->currentDepartmentHeadName !== null;
    }

    // =========================================================
    //  VALIDATION
    // =========================================================

    protected function rules(): array
    {
        return [
            'user_id'       => ['required', 'integer', Rule::exists('users', 'id')],
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'       => 'You must select a user.',
            'user_id.exists'         => 'Selected user does not exist.',
            'department_id.required' => 'You must select a department.',
            'department_id.exists'   => 'Selected department does not exist.',
        ];
    }

    // =========================================================
    //  CREATE
    // =========================================================

    public function create()
    {
        $validated = $this->validate();

        // If we're replacing an existing head, require confirmation.
        if ($this->isReassigning && ! $this->confirmReassign) {
            $this->addError(
                'confirmReassign',
                'This department already has a program head. Tick the confirmation box to proceed.'
            );
            return;
        }

        // Validate role without hydrating the full model.
        $hasRole = User::role('program head')
            ->whereKey($validated['user_id'])
            ->exists();

        if (! $hasRole) {
            $this->addError('user_id', 'Selected user must already have the Program Head role.');
            return;
        }

        try {
            DB::transaction(function () use ($validated) {
                // Lock row → protects against two admins assigning simultaneously.
                $department = Department::query()
                    ->whereKey($validated['department_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $department->update([
                    'program_head_id' => $validated['user_id'],
                ]);
            });
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Could not assign program head. Please try again.');
            return;
        }

        // Bust the caches so both pages reflect the change immediately.
        Cache::forget('assign:active-departments:v2');
        Cache::forget('dept:ph_count');

        $userName = collect($this->users)
            ->firstWhere('id', $validated['user_id'])['name'] ?? 'User';

        $deptName = collect($this->departments)
            ->firstWhere('id', $validated['department_id'])['dept_name'] ?? 'Department';

        session()->flash('success', "{$userName} assigned as Program Head of {$deptName}.");

        return redirect()->route('super-admin.assign.view');
    }
};