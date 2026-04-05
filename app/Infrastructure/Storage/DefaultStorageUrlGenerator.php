<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Application\Contracts\StorageUrlGenerator;
use Illuminate\Support\Facades\Storage;

final class DefaultStorageUrlGenerator implements StorageUrlGenerator
{
    public function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $url = Storage::url($path);
        $parts = parse_url($url);

        if ($parts === false) {
            return $url;
        }

        $path = $parts['path'] ?? null;
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        return $path !== null ? $path . $query : $url;
    }
}
