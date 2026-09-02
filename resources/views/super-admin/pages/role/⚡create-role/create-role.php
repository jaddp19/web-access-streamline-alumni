<?php

use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public $name = '';
    public $selectedPermissions = [];

    #[Computed]
    public function permissions()
    {
        return Permission::select('id', 'name')->orderBy('name')->get();
    }

    public function rules()
    {
        return [
            'name'                  => 'required|string|min:3|unique:roles,name',
            'selectedPermissions'   => 'required|array|min:1',
            'selectedPermissions.*' => 'exists:permissions,name',
        ];
    }

    public function messages()
    {
        return [
            'name.required'                 => 'The role name is required.',
            'name.min'                      => 'The role name must be at least 3 characters.',
            'name.unique'                   => 'The role name is already taken.',
            'selectedPermissions.required'  => 'Please select at least one permission.',
            'selectedPermissions.min'       => 'Please select at least one permission.',
        ];
    }

    public function save()
    {
        $this->validate();

        $roleName = Str::of($this->name)->trim()->title()->lower()->toString();

        $role = Role::create([
            'name'       => $roleName,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($this->selectedPermissions);

        session()->flash('success', 'Role created successfully.');

        $this->reset(['name', 'selectedPermissions']);

        return redirect()->route('view-role');
    }
};
