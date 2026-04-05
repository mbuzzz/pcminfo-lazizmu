<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PostStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Review = 'review';
    case Published = 'published';
    case Archived = 'archived';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Review => 'Menunggu Review',
            self::Published => 'Diterbitkan',
            self::Archived => 'Diarsipkan',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Review => 'warning',
            self::Published => 'success',
            self::Archived => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil',
            self::Review => 'heroicon-o-eye',
            self::Published => 'heroicon-o-globe-alt',
            self::Archived => 'heroicon-o-archive-box',
        };
    }
}
