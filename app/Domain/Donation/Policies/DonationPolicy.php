<?php

declare(strict_types=1);

namespace App\Domain\Donation\Policies;

use App\Models\User;

class DonationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_donations');
    }

    public function view(User $user): bool
    {
        return $user->can('manage_donations');
    }

    public function create(User $user): bool
    {
        return $user->can('manage_donations');
    }

    public function update(User $user): bool
    {
        return $user->can('manage_donations') || $user->can('verify_donations');
    }
}
