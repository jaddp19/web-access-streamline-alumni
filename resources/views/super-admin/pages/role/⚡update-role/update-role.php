<?php

use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.app-super-admin')] class extends Component
{
    public Role $role;
    public string $name = '';
    public array $selectedPermissions = [];

    public function mount(Role $role)
    {
        $this->role = $role;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
    }

    #[Computed]
    public function permissions()
    {
        return Permission::select('id', 'name')->orderBy('name')->get();
    }

    protected function rules()
    {
        return [
            'name'                  => 'required|string|min:3|unique:roles,name,' . $this->role->id,
            'selectedPermissions'   => 'required|array|min:1',
            'selectedPermissions.*' => 'exists:permissions,name',
        ];
    }

    public function messages()
    {
        return [
            'name.required'                => 'The role name is required.',
            'name.min'                     => 'The role name must be at least 3 characters.',
            'name.unique'                  => 'The role name is already taken.',
            'selectedPermissions.required' => 'Please select at least one permission.',
            'selectedPermissions.min'      => 'Please select at least one permission.',
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        $this->role->update([
            'name' => Str::of($validated['name'])->trim()->lower()->toString(),
        ]);

        $this->role->syncPermissions($validated['selectedPermissions']);

        session()->flash('success', 'Role updated successfully!');
        return redirect()->route('view-role');
    }
};
