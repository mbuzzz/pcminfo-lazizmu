<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum InstitutionType: string implements HasColor, HasIcon, HasLabel
{
    case School = 'school';
    case Kindergarten = 'kindergarten';
    case Clinic = 'clinic';
    case Mosque = 'mosque';
    case Finance = 'finance';
    case Enterprise = 'enterprise';
    case Social = 'social';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::School => 'Sekolah',
            self::Kindergarten => 'TK / PAUD',
            self::Clinic => 'Klinik / RS',
            self::Mosque => 'Masjid / Mushola',
            self::Finance => 'Lazismu / BMT',
            self::Enterprise => 'Unit Usaha / BUMM',
            self::Social => 'Sosial / Panti',
            self::Other => 'Lainnya',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::School => 'primary',
            self::Kindergarten => 'warning',
            self::Clinic => 'danger',
            self::Mosque => 'success',
            self::Finance => 'info',
            self::Enterprise => 'warning',
            self::Social => 'success',
            self::Other => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::School => 'heroicon-o-academic-cap',
            self::Kindergarten => 'heroicon-o-face-smile',
            self::Clinic => 'heroicon-o-heart',
            self::Mosque => 'heroicon-o-building-library',
            self::Finance => 'heroicon-o-banknotes',
            self::Enterprise => 'heroicon-o-building-storefront',
            self::Social => 'heroicon-o-home',
            self::Other => 'heroicon-o-squares-plus',
        };
    }
}
