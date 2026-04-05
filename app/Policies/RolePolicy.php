<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_roles') || $user->can('view_any_roles');
    }

    public function view(User $user, Role $role): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('manage_roles') || $user->can('create_roles');
    }

    public function update(User $user, Role $role): bool
    {
        if (! ($user->can('manage_roles') || $user->can('update_roles'))) {
            return false;
        }

        if ($role->isCoreRole() && ! $user->isCoreAdministrator()) {
            return false;
        }

        return true;
    }

    public function delete(User $user, Role $role): bool
    {
        if (! ($user->can('manage_roles') || $user->can('delete_roles'))) {
            return false;
        }

        if ($role->isCoreRole()) {
            return false;
        }

        if ($role->users()->exists()) {
            return false;
        }

        return true;
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('manage_roles') || $user->can('delete_roles');
    }
}
