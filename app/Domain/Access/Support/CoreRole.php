<?php

declare(strict_types=1);

namespace App\Domain\Access\Support;

final class CoreRole
{
    public const SuperAdmin = 'super_admin';
    public const AdminPcm = 'admin_pcm';
    public const AdminLazismu = 'admin_lazismu';
    public const Kontributor = 'kontributor';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::SuperAdmin,
            self::AdminPcm,
            self::AdminLazismu,
            self::Kontributor,
        ];
    }
}
