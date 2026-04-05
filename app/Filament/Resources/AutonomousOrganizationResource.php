<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\OrganizationUnitType;
use App\Filament\Resources\AutonomousOrganizationResource\Pages;
use App\Filament\Resources\OrganizationUnits\BaseOrganizationUnitResource;
use BackedEnum;

class AutonomousOrganizationResource extends BaseOrganizationUnitResource
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 5;

    protected static function getUnitType(): OrganizationUnitType
    {
        return OrganizationUnitType::AutonomousOrganization;
    }

    protected static function getPermissionName(): string
    {
        return 'manage_autonomous_organizations';
    }

    protected static function getEntityLabel(): string
    {
        return 'Organisasi Otonom';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAutonomousOrganizations::route('/'),
            'create' => Pages\CreateAutonomousOrganization::route('/create'),
            'edit' => Pages\EditAutonomousOrganization::route('/{record}/edit'),
        ];
    }
}
