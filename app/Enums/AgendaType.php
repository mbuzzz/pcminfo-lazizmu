<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AgendaType: string implements HasColor, HasIcon, HasLabel
{
    case Kajian = 'kajian';
    case Meeting = 'meeting';
    case Social = 'social';
    case Education = 'education';
    case Competition = 'competition';
    case Commemoration = 'commemoration';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Kajian => 'Kajian / Pengajian',
            self::Meeting => 'Rapat / Musyawarah',
            self::Social => 'Kegiatan Sosial',
            self::Education => 'Seminar / Pelatihan',
            self::Competition => 'Lomba / Olimpiade',
            self::Commemoration => 'Peringatan Hari Besar',
            self::Other => 'Lainnya',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Kajian => 'success',
            self::Meeting => 'info',
            self::Social => 'warning',
            self::Education => 'primary',
            self::Competition => 'danger',
            self::Commemoration => 'success',
            self::Other => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Kajian => 'heroicon-o-book-open',
            self::Meeting => 'heroicon-o-user-group',
            self::Social => 'heroicon-o-heart',
            self::Education => 'heroicon-o-academic-cap',
            self::Competition => 'heroicon-o-trophy',
            self::Commemoration => 'heroicon-o-star',
            self::Other => 'heroicon-o-squares-plus',
        };
    }
}
