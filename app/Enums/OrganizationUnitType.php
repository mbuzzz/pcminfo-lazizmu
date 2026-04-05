<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrganizationUnitType: string implements HasColor, HasIcon, HasLabel
{
    case AutonomousOrganization = 'autonomous_organization';
    case Council = 'council';
    case Agency = 'agency';

    public function getLabel(): string
    {
        return match ($this) {
            self::AutonomousOrganization => 'Organisasi Otonom',
            self::Council => 'Majelis',
            self::Agency => 'Lembaga',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::AutonomousOrganization => 'info',
            self::Council => 'warning',
            self::Agency => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::AutonomousOrganization => 'heroicon-o-users',
            self::Council => 'heroicon-o-rectangle-group',
            self::Agency => 'heroicon-o-building-office-2',
        };
    }
}
