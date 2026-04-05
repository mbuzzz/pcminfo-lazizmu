<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LeaderOrganization: string implements HasColor, HasIcon, HasLabel
{
    case Pcm = 'pcm';
    case Pcw = 'pcw';
    case Lazismu = 'lazismu';
    case Institution = 'institution';
    case Ortom = 'ortom';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pcm => 'PCM (Pimpinan Cabang Muhammadiyah)',
            self::Pcw => 'PCA (Pimpinan Cabang Aisyiyah)',
            self::Lazismu => 'Lazismu Cabang',
            self::Institution => 'Amal Usaha / Lembaga',
            self::Ortom => 'Organisasi Otonom (Ortom)',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pcm => 'primary',
            self::Pcw => 'warning',
            self::Lazismu => 'success',
            self::Institution => 'info',
            self::Ortom => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pcm => 'heroicon-o-building-office',
            self::Pcw => 'heroicon-o-user-group',
            self::Lazismu => 'heroicon-o-heart',
            self::Institution => 'heroicon-o-building-library',
            self::Ortom => 'heroicon-o-star',
        };
    }
}
