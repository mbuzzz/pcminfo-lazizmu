<?php

declare(strict_types=1);

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class MediaUploadService
{
    public function disk(): string
    {
        return (string) config('media.disk', 'public');
    }

    public function visibility(): string
    {
        return (string) config('media.visibility', 'public');
    }

    public function generateFilename(UploadedFile $file, ?string $prefix = null): string
    {
        $base = $prefix !== null && $prefix !== ''
            ? Str::slug($prefix)
            : Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        $base = $base !== '' ? $base : 'file';
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');

        return sprintf(
            '%s-%s-%s.%s',
            $base,
            now()->format('YmdHis'),
            Str::lower(Str::random(8)),
            $extension,
        );
    }

    public function url(?string $path, ?string $disk = null): ?string
    {
        if (blank($path)) {
            return null;
        }

        return $this->normalizeStorageUrl(
            Storage::disk($disk ?: $this->disk())->url($path),
            $disk ?: $this->disk(),
        );
    }

    public function delete(?string $path, ?string $disk = null): void
    {
        if (blank($path)) {
            return;
        }

        $storage = Storage::disk($disk ?: $this->disk());

        if ($storage->exists($path)) {
            $storage->delete($path);
        }
    }

    public function replace(?string $oldPath, ?string $newPath, ?string $disk = null): void
    {
        if (blank($oldPath) || blank($newPath) || $oldPath === $newPath) {
            return;
        }

        $this->delete($oldPath, $disk);
    }

    public function sync(?string $oldPath, mixed $newPath, bool $fieldWasSubmitted, ?string $disk = null): void
    {
        if (! $fieldWasSubmitted || blank($oldPath)) {
            return;
        }

        if (blank($newPath) || $oldPath !== $newPath) {
            $this->delete($oldPath, $disk);
        }
    }

    private function normalizeStorageUrl(string $url, string $disk): string
    {
        if (! in_array($disk, ['public', 'local'], true)) {
            return $url;
        }

        $parts = parse_url($url);

        if ($parts === false) {
            return $url;
        }

        $path = $parts['path'] ?? null;
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        return $path !== null ? $path . $query : $url;
    }
}
