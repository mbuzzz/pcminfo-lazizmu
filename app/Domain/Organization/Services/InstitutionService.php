<?php

declare(strict_types=1);

namespace App\Domain\Organization\Services;

use App\Models\Institution;
use App\Services\Media\MediaUploadService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class InstitutionService
{
    public function __construct(
        private readonly MediaUploadService $mediaUploadService,
    ) {
    }

    public function create(array $data): Institution
    {
        return DB::transaction(function () use ($data): Institution {
            $payload = $this->normalize($data);

            $this->ensureUniqueSlug($payload['slug']);

            return Institution::query()->create($payload)->refresh();
        });
    }

    public function update(Institution $institution, array $data): Institution
    {
        return DB::transaction(function () use ($institution, $data): Institution {
            $logoWasSubmitted = array_key_exists('logo', $data);
            $coverWasSubmitted = array_key_exists('cover_image', $data);
            $payload = $this->normalize($data, $institution);

            $this->ensureUniqueSlug($payload['slug'], $institution);
            $this->mediaUploadService->sync($institution->logo, $payload['logo'] ?? $institution->logo, $logoWasSubmitted);
            $this->mediaUploadService->sync($institution->cover_image, $payload['cover_image'] ?? $institution->cover_image, $coverWasSubmitted);

            $institution->update($payload);

            return $institution->refresh();
        });
    }

    private function normalize(array $data, ?Institution $institution = null): array
    {
        $data['slug'] = filled(Arr::get($data, 'slug'))
            ? Str::slug((string) $data['slug'])
            : Str::slug((string) Arr::get($data, 'name', $institution?->name));

        $data['order'] = (int) Arr::get($data, 'order', $institution?->order ?? 0);
        $data['is_featured'] = (bool) Arr::get($data, 'is_featured', $institution?->is_featured ?? false);

        return $data;
    }

    private function ensureUniqueSlug(string $slug, ?Institution $ignore = null): void
    {
        $query = Institution::query()->where('slug', $slug);

        if ($ignore !== null) {
            $query->whereKeyNot($ignore->getKey());
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'Slug amal usaha sudah digunakan.',
            ]);
        }
    }
}
