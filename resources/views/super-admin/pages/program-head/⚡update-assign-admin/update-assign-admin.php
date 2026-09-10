<?php

use App\Models\Department;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public ?int $user_id = null;
    public ?int $department_id = null;
    public Department $department;

    public function mount(Department $department)
    {
        $this->department = $department;
        $this->user_id = $department->program_head_id;
        $this->department_id = $department->id;
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

        // Ensure user has the program head role
        if (! $newUser->hasRole('program head')) {
            $this->addError('user_id', 'Selected user must already have the Program Head role.');
            return;
        }

        // Update assignment
        $department = Department::findOrFail($validated['department_id']);
        $department->program_head_id = $newUser->id;
        $department->save();

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
