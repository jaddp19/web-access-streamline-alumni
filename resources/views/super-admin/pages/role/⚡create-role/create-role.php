<?php

use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

new #[Layout('layouts::app-super-admin')] class extends Component
{
    public $name;
    public $selectedPermissions = [];

    public function mount()
    {
        $this->permissions(); //call inside the mount/constructor method
    }

    //get all permissions name from the permissions table
    #[Computed()]
    public function permissions()
    {
        return Permission::select('id','name')->get();
    }


    //validation of data from the user input/from model binding
    public function rules()
    {
        return [
            'name' => 'required|string|min:3|unique:roles,name',
            'selectedPermissions' => 'required|array|min:1',
            'selectedPermissions.*' => 'exists:permissions,name',
        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'The role name is required.',
            'name.unique' => 'The role name must be unique/already taken.',
            'selectedPermissions.required' => 'Please select at least one permission.',
        ];
    }


    //save method with validation, sanitization adn saving data to table
    public function save()
    {
        $this->validate();

        //sanitize
        $roleName = Str::of($this->name)->trim()->title()->lower();

        //1. create role
        $role = Role::create([
            'name' => $roleName
        ]);

        //2. sync permissions
        $role->syncPermissions($this->selectedPermissions);

        session()->flash('success', 'Role created successfully.');

        $this->reset([
            'name', 'selectedPermissions'
        ]);

        return redirect()->route('view-role');
    }
};