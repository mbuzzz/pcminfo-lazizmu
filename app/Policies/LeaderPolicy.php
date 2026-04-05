<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Leader;
use App\Models\User;

class LeaderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_leadership_structure');
    }

    public function view(User $user, Leader $leader): bool
    {
        return $user->can('manage_leadership_structure');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_leadership_structure');
    }

    public function update(User $user, Leader $leader): bool
    {
        return $user->can('manage_leadership_structure');
    }

    public function delete(User $user, Leader $leader): bool
    {
        return $user->can('manage_leadership_structure');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('manage_leadership_structure');
    }
}
