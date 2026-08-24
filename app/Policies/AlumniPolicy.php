<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class AlumniPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserProfile $alumni)
    {
        // TODO: rebuild against the new schema. Old logic relied on
        // EducationalBackground -> DegreeProgram.department, which no longer exists.
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, $alumniProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, $alumniProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, $alumniProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, $alumniProfile): bool
    {
        return false;
    }
}
