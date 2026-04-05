<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\OrganizationUnitType;
use App\Filament\Resources\CouncilResource\Pages;
use App\Filament\Resources\OrganizationUnits\BaseOrganizationUnitResource;
use BackedEnum;

class CouncilResource extends BaseOrganizationUnitResource
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?int $navigationSort = 4;

    protected static function getUnitType(): OrganizationUnitType
    {
        return OrganizationUnitType::Council;
    }

    protected static function getPermissionName(): string
    {
        return 'manage_councils';
    }

    protected static function getEntityLabel(): string
    {
        return 'Majelis';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCouncils::route('/'),
            'create' => Pages\CreateCouncil::route('/create'),
            'edit' => Pages\EditCouncil::route('/{record}/edit'),
        ];
    }
}
