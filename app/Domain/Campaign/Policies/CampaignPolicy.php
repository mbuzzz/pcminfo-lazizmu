<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Policies;

use App\Models\User;

class CampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_campaigns');
    }

    public function view(User $user): bool
    {
        return $user->can('manage_campaigns');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_campaigns');
    }

    public function update(User $user): bool
    {
        return $user->can('manage_campaigns');
    }

    public function delete(User $user): bool
    {
        return $user->can('manage_campaigns');
    }
}
