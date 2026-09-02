<?php

use App\Models\Department;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public ?int $user_id = null;
    public ?int $department_id = null;

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

    public function create()
    {
        $validated = $this->validate();

        $user = User::findOrFail($validated['user_id']);
        $roleName = 'program-head-' . $validated['department_id'];

        if ($user->hasRole($roleName)) {
            $this->addError('user_id', 'This user is already the program head for that department.');
            return;
        }

        $role = Role::firstOrCreate(['name' => $roleName]);

        $user->assignRole($role);

        session()->flash('success', 'Program Head assigned successfully.');
        return redirect()->route('super-admin.assign.view');
    }

    #[Computed]
    public function users()
    {
        return User::role('admin')
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
