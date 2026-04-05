<?php

declare(strict_types=1);

namespace App\Enums;

enum PostReactionType: string
{
    case Like = 'like';
    case Love = 'love';
    case Insightful = 'insightful';
    case Support = 'support';

    public function label(): string
    {
        return match ($this) {
            self::Like => 'Suka',
            self::Love => 'Love',
            self::Insightful => 'Mencerahkan',
            self::Support => 'Dukungan',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $type): string => $type->value,
            self::cases(),
        );
    }
}
