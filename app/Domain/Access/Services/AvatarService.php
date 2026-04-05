<?php

declare(strict_types=1);

namespace App\Domain\Access\Services;

use App\Services\Media\MediaUploadService;

final class AvatarService
{
    public function __construct(
        private readonly MediaUploadService $mediaUploadService,
    ) {
    }

    public function url(?string $path): ?string
    {
        return $this->mediaUploadService->url($path);
    }

    public function replace(?string $oldPath, ?string $newPath): void
    {
        $this->mediaUploadService->replace($oldPath, $newPath);
    }

    public function sync(?string $oldPath, mixed $newPath, bool $fieldWasSubmitted): void
    {
        $this->mediaUploadService->sync($oldPath, $newPath, $fieldWasSubmitted);
    }

    public function delete(?string $path): void
    {
        $this->mediaUploadService->delete($path);
    }
}
