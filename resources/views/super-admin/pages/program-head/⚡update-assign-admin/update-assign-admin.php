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
    public Department $department;
    public ?int $user_id = null;
    public ?int $department_id = null;

    public function mount(Department $department): void
    {
        $this->department    = $department;
        $this->user_id       = $department->program_head_id;
        $this->department_id = $department->id;
    }

    // =========================================================
    //  COMPUTED
    // =========================================================

    #[Computed]
    public function users(): array
    {
        $cacheKey = 'assign:program-head-users:v2';
        $cached   = Cache::get($cacheKey);

        if (! is_array($cached)) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if ($cached === null) {
            $cached = User::role('program head')
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn ($u) => ['id' => (int) $u->id, 'name' => (string) $u->name])
                ->all();

            Cache::put($cacheKey, $cached, now()->addMinutes(5));
        }

        return $cached;
    }

    #[Computed]
    public function departments(): array
    {
        $cacheKey = 'assign:active-departments:v2';
        $cached   = Cache::get($cacheKey);

        if (! is_array($cached)) {
            Cache::forget($cacheKey);
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

            Cache::put($cacheKey, $cached, now()->addMinutes(5));
        }

        return $cached;
    }

    #[Computed]
    public function currentHeadName(): ?string
    {
        if (! $this->department->program_head_id) {
            return null;
        }

        $fromList = collect($this->users)
            ->firstWhere('id', $this->department->program_head_id);

        return $fromList['name']
            ?? User::query()->whereKey($this->department->program_head_id)->value('name');
    }

    /**
     * If the picked user currently heads ANOTHER department,
     * return that department's info so we can show a "will be moved" notice.
     *
     * Reads from the cached $this->departments array — 0 queries.
     */
    #[Computed]
    public function willBeMovedFrom(): ?array
    {
        if (! $this->user_id) {
            return null;
        }

        $found = collect($this->departments)
            ->first(fn ($d) => $d['program_head_id'] === (int) $this->user_id
                            && $d['id'] !== $this->department_id);

        return $found ? ['id' => $found['id'], 'dept_name' => $found['dept_name']] : null;
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
    //  UPDATE
    // =========================================================

    public function update()
    {
        $validated = $this->validate();

        // Role check — no hydration.
        $hasRole = User::role('program head')
            ->whereKey($validated['user_id'])
            ->exists();

        if (! $hasRole) {
            $this->addError('user_id', 'Selected user must already have the Program Head role.');
            return;
        }

        try {
            DB::transaction(function () use ($validated) {
                // ✅ If this user is already head of ANOTHER department,
                //    unassign them there first (move behavior).
                Department::query()
                    ->where('program_head_id', $validated['user_id'])
                    ->where('id', '!=', $validated['department_id'])
                    ->update(['program_head_id' => null]);

                // Then assign them here.
                Department::query()
                    ->whereKey($validated['department_id'])
                    ->lockForUpdate()
                    ->firstOrFail()
                    ->update(['program_head_id' => $validated['user_id']]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'uniq_departments_program_head')) {
                $this->addError(
                    'user_id',
                    'Assignment conflict — please refresh and try again.'
                );
                return;
            }

            report($e);
            session()->flash('error', 'Could not update program head. Please try again.');
            return;
        } catch (\Throwable $e) {
            report($e);
            session()->flash('error', 'Could not update program head. Please try again.');
            return;
        }

        Cache::forget('assign:active-departments:v2');
        Cache::forget('dept:ph_count');

        session()->flash('success', 'Program Head updated successfully.');

        return redirect()->route('super-admin.assign.view');
    }
};