<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\OrganizationUnitType;
use App\Filament\Resources\AgencyResource\Pages;
use App\Filament\Resources\OrganizationUnits\BaseOrganizationUnitResource;
use BackedEnum;

class AgencyResource extends BaseOrganizationUnitResource
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 3;

    protected static function getUnitType(): OrganizationUnitType
    {
        return OrganizationUnitType::Agency;
    }

    protected static function getPermissionName(): string
    {
        return 'manage_agencies';
    }

    protected static function getEntityLabel(): string
    {
        return 'Lembaga';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgencies::route('/'),
            'create' => Pages\CreateAgency::route('/create'),
            'edit' => Pages\EditAgency::route('/{record}/edit'),
        ];
    }
}
