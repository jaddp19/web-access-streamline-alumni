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
    $department = Department::findOrFail($validated['department_id']);

    // Ensure user has the program head role
    if (! $user->hasRole('program head')) {
        session()->flash('error', 'Selected user must already have the Program Head role.');
        return;
    }

    // Assign user as program head of the department
    $department->program_head_id = $user->id;
    $department->save();

    session()->flash('success', "{$user->name} assigned as Program Head of {$department->dept_name}.");
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
