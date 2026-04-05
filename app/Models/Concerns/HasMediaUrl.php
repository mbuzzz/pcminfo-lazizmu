<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Services\Media\MediaUploadService;

trait HasMediaUrl
{
    protected function mediaUrl(?string $path): ?string
    {
        return app(MediaUploadService::class)->url($path);
    }
}
