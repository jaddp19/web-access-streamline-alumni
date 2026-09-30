<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    protected array $protected = ['registrar', 'program head', 'alumni'];

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('can_view_any');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('can_view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('can_create');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('can_update')
            && ! $this->isProtected($role);
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('can_delete')
            && ! $this->isProtected($role);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('can_delete');
    }

    protected function isProtected(Role $role): bool
    {
        return in_array($role->name, $this->protected, true);
    }
}