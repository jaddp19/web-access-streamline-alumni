<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('can_view_any');
    }

    public function view(User $user, User $target): bool
    {
        return $user->hasPermissionTo('can_view')
            || $user->id === $target->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('can_create');
    }

    public function update(User $user, User $target): bool
    {
        // Nobody edits a super-admin except a super-admin.
        if ($target->hasRole('super-admin') && ! $user->hasRole('super-admin')) {
            return false;
        }

        return $user->hasPermissionTo('can_update')
            || $user->id === $target->id;
    }

    public function delete(User $user, User $target): bool
    {
        // Never delete yourself; never delete a super-admin.
        if ($user->id === $target->id) {
            return false;
        }

        if ($target->hasRole('super-admin')) {
            return false;
        }

        return $user->hasPermissionTo('can_delete');
    }

    /**
     * Bulk delete — no specific target instance, so it's a class-level check.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('can_delete');
    }

    public function export(User $user): bool
    {
        return $user->hasPermissionTo('can_view_any');
    }
}