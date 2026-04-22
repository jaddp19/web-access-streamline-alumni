<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

new #[Layout('layouts::app-super-admin')] class extends Component
{
    public $role;
    public $name;
    public $selectedPermissions = [];

    public function mount(Role $role)
    {
        $this->role = $role;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
    }

    #[Computed()]
    public function permissions()
    {
        return Permission::select('id','name')->get();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|unique:roles,name,' . $this->role->id,
            'selectedPermissions' => 'array|min:1'
        ];
    }

    public function save()
    {
        $this->validate();

        // Update role name
        $this->role->update([
            'name' => $this->name,
        ]);

        // Sync permissions
        $this->role->syncPermissions($this->selectedPermissions);

        session()->flash('success', 'Role updated successfully!');
        return redirect()->route('view-role');
    }
};