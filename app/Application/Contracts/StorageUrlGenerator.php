<?php

declare(strict_types=1);

namespace App\Application\Contracts;

interface StorageUrlGenerator
{
    public function url(?string $path): ?string;
}
