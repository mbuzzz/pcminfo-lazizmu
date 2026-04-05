<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum AgendaStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Published = 'published';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Dipublikasikan',
            self::Cancelled => 'Dibatalkan',
            self::Completed => 'Selesai',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'success',
            self::Cancelled => 'danger',
            self::Completed => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil',
            self::Published => 'heroicon-o-calendar',
            self::Cancelled => 'heroicon-o-x-circle',
            self::Completed => 'heroicon-o-check-badge',
        };
    }
}
