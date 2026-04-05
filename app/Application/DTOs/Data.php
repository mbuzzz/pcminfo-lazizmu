<?php

declare(strict_types=1);

namespace App\Application\DTOs;

abstract class Data
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): static
    {
        return new static(...$attributes);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
