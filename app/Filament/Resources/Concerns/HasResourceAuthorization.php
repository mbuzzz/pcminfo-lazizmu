<?php

declare(strict_types=1);

namespace App\Filament\Resources\Concerns;

trait HasResourceAuthorization
{
    protected static function getResourcePermission(): string
    {
        return property_exists(static::class, 'permission')
            ? (string) static::$permission
            : '';
    }

    protected static function canAccessResource(): bool
    {
        if (static::getResourcePermission() === '') {
            return true;
        }

        return auth()->user()?->can(static::getResourcePermission()) ?? false;
    }

    public static function canViewAny(): bool
    {
        return static::canAccessResource();
    }

    public static function canCreate(): bool
    {
        return static::canAccessResource();
    }

    public static function canEdit($record): bool
    {
        return static::canAccessResource();
    }

    public static function canDelete($record): bool
    {
        return static::canAccessResource();
    }

    public static function canDeleteAny(): bool
    {
        return static::canAccessResource();
    }
}
