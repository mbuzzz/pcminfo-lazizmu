<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PostType: string implements HasColor, HasIcon, HasLabel
{
    case News = 'news';
    case Article = 'article';
    case Announcement = 'announcement';
    case Study = 'study';

    public function getLabel(): string
    {
        return match ($this) {
            self::News => 'Berita',
            self::Article => 'Artikel',
            self::Announcement => 'Pengumuman',
            self::Study => 'Kajian',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::News => 'info',
            self::Article => 'primary',
            self::Announcement => 'warning',
            self::Study => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::News => 'heroicon-o-newspaper',
            self::Article => 'heroicon-o-document-text',
            self::Announcement => 'heroicon-o-megaphone',
            self::Study => 'heroicon-o-book-open',
        };
    }
}
