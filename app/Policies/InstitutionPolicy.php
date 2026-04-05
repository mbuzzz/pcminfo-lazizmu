<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Institution;
use App\Models\User;

class InstitutionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_business_units');
    }

    public function view(User $user, Institution $institution): bool
    {
        return $user->can('manage_business_units');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_business_units');
    }

    public function update(User $user, Institution $institution): bool
    {
        return $user->can('manage_business_units');
    }

    public function delete(User $user, Institution $institution): bool
    {
        return $user->can('manage_business_units');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('manage_business_units');
    }

    public function restore(User $user, Institution $institution): bool
    {
        return $user->can('manage_business_units');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('manage_business_units');
    }

    public function forceDelete(User $user, Institution $institution): bool
    {
        return $user->can('manage_business_units');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('manage_business_units');
    }
}
