<?php

declare(strict_types=1);

namespace App\Domain\Access\Services;

use App\Domain\Access\Enums\UserRoleEnum;
use App\Models\User;

final class PanelAccessService
{
    public function canAccessAdminPanel(User $user): bool
    {
        if (app()->environment('local')) {
            return true;
        }

        return $user->hasAnyRole(UserRoleEnum::values());
    }
}
