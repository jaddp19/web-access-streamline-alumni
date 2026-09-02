<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public string $programHead = ''; // "{roleId}-{userId}" — matches the route param name exactly
    public Role $role;
    public User $user;
    public ?int $user_id = null;
    public ?int $department_id = null;

    public function mount(string $programHead)
    {
        $this->programHead = $programHead;

        [$roleId, $userId] = explode('-', $programHead, 2);

        $this->role = Role::findOrFail($roleId);
        $this->user = User::findOrFail($userId);

        $this->user_id = $this->user->id;
        $this->department_id = (int) Str::after($this->role->name, 'program-head-');
    }

    protected function rules()
    {
        return [
            'user_id'       => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required'       => 'You must select a user.',
            'user_id.exists'         => 'Selected user does not exist.',
            'department_id.required' => 'You must select a department.',
            'department_id.exists'   => 'Selected department does not exist.',
        ];
    }

    public function update()
    {
        $validated = $this->validate();

        $newUser = User::findOrFail($validated['user_id']);
        $newRoleName = 'program-head-' . $validated['department_id'];

        $isUnchanged = $newUser->id === $this->user->id && $newRoleName === $this->role->name;

        if (! $isUnchanged && $newUser->hasRole($newRoleName)) {
            $this->addError('user_id', 'This user is already the program head for that department.');
            return;
        }

        if (! $isUnchanged) {
            // Remove the old assignment.
            $this->user->removeRole($this->role);
            if ($this->role->users()->count() === 0) {
                $this->role->delete();
            }

            // Apply the new assignment.
            $newRole = Role::firstOrCreate(['name' => $newRoleName]);
            $newUser->assignRole($newRole);
        }

        session()->flash('success', 'Program Head updated successfully.');
        return redirect()->route('super-admin.assign.view');
    }

    #[Computed]
    public function users()
    {
        return User::role('program head')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function departments()
    {
        return Department::select('id', 'dept_name')
            ->where('is_active', true)
            ->orderBy('dept_name')
            ->get();
    }
};
