<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Filament\Support\Contracts\HasLabel;

final class EnumOptions
{
    /**
     * @param  class-string  $enumClass
     * @return array<string, string>
     */
    public static function make(string $enumClass): array
    {
        $options = [];

        foreach ($enumClass::cases() as $case) {
            $options[$case->value] = $case instanceof HasLabel
                ? $case->getLabel()
                : $case->name;
        }

        return $options;
    }
}
