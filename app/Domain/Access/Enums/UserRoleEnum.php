<?php

declare(strict_types=1);

namespace App\Domain\Access\Enums;

enum UserRoleEnum: string
{
    case SuperAdmin = 'super_admin';
    case AdminPcm = 'admin_pcm';
    case AdminLazismu = 'admin_lazismu';
    case Kontributor = 'kontributor';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $role): string => $role->value, self::cases());
    }
}
