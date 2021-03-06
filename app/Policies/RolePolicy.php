<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any roles.
     *
     * @param  \App\Models\User  $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can('roles:view');
    }

    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\Models\User  $user
     *
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('roles:view');
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\Models\User  $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('roles:create');
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\Models\User  $user
     *
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('roles:edit');
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\Models\User  $user
     *
     * @return mixed
     */
    public function delete(User $user, Role $role)
    {
        return $user->can('roles:delete') && ! $user->hasRole($role);
    }
}
