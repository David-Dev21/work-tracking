<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AreaRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class AreaRolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_area::role');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AreaRole $areaRole): bool
    {
        return $user->can('view_area::role');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_area::role');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AreaRole $areaRole): bool
    {
        return $user->can('update_area::role');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AreaRole $areaRole): bool
    {
        return $user->can('delete_area::role');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_area::role');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, AreaRole $areaRole): bool
    {
        return $user->can('force_delete_area::role');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_area::role');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, AreaRole $areaRole): bool
    {
        return $user->can('restore_area::role');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_area::role');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, AreaRole $areaRole): bool
    {
        return $user->can('replicate_area::role');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_area::role');
    }
}
