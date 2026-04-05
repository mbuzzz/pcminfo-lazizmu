<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DistributionStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Distributed = 'distributed';
    case Reported = 'reported';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Approved => 'Disetujui',
            self::Distributed => 'Tersalurkan',
            self::Reported => 'Dilaporkan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Approved => 'warning',
            self::Distributed => 'success',
            self::Reported => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil',
            self::Approved => 'heroicon-o-check-circle',
            self::Distributed => 'heroicon-o-truck',
            self::Reported => 'heroicon-o-document-text',
        };
    }
}
