<?php

declare(strict_types=1);

namespace App\Domain\Organization\Services;

use App\Enums\OrganizationUnitType;
use App\Models\OrganizationUnit;
use App\Services\Media\MediaUploadService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class OrganizationUnitService
{
    public function __construct(
        private readonly MediaUploadService $mediaUploadService,
    ) {
    }

    public function create(array $data, OrganizationUnitType $type): OrganizationUnit
    {
        return DB::transaction(function () use ($data, $type): OrganizationUnit {
            $payload = $this->normalize($data, $type);

            $this->ensureUniqueSlug($payload['slug'], $payload['type']);

            return OrganizationUnit::query()->create($payload)->refresh();
        });
    }

    public function update(OrganizationUnit $organizationUnit, array $data): OrganizationUnit
    {
        return DB::transaction(function () use ($organizationUnit, $data): OrganizationUnit {
            $logoWasSubmitted = array_key_exists('logo', $data);
            $payload = $this->normalize($data, $organizationUnit->type, $organizationUnit);

            $this->ensureUniqueSlug($payload['slug'], $payload['type'], $organizationUnit);
            $this->mediaUploadService->sync($organizationUnit->logo, $payload['logo'], $logoWasSubmitted);

            $organizationUnit->update($payload);

            return $organizationUnit->refresh();
        });
    }

    public function delete(OrganizationUnit $organizationUnit): void
    {
        $organizationUnit->delete();
    }

    /**
     * @return array{
     *     type: string,
     *     name: string,
     *     slug: string,
     *     acronym: ?string,
     *     tagline: ?string,
     *     description: ?string,
     *     logo: ?string,
     *     chairperson: ?string,
     *     secretary: ?string,
     *     phone: ?string,
     *     email: ?string,
     *     website: ?string,
     *     address: ?string,
     *     meta: array<mixed>,
     *     is_active: bool,
     *     sort_order: int
     * }
     */
    private function normalize(array $data, OrganizationUnitType $type, ?OrganizationUnit $organizationUnit = null): array
    {
        $name = trim((string) Arr::get($data, 'name', $organizationUnit?->name));
        $slug = Str::slug((string) Arr::get($data, 'slug', $organizationUnit?->slug ?: $name));

        return [
            'type' => $type->value,
            'name' => $name,
            'slug' => $slug,
            'acronym' => $this->nullableString(Arr::get($data, 'acronym', $organizationUnit?->acronym)),
            'tagline' => $this->nullableString(Arr::get($data, 'tagline', $organizationUnit?->tagline)),
            'description' => $this->nullableString(Arr::get($data, 'description', $organizationUnit?->description)),
            'logo' => Arr::get($data, 'logo', $organizationUnit?->logo),
            'chairperson' => $this->nullableString(Arr::get($data, 'chairperson', $organizationUnit?->chairperson)),
            'secretary' => $this->nullableString(Arr::get($data, 'secretary', $organizationUnit?->secretary)),
            'phone' => $this->nullableString(Arr::get($data, 'phone', $organizationUnit?->phone)),
            'email' => $this->nullableString(Arr::get($data, 'email', $organizationUnit?->email)),
            'website' => $this->nullableString(Arr::get($data, 'website', $organizationUnit?->website)),
            'address' => $this->nullableString(Arr::get($data, 'address', $organizationUnit?->address)),
            'meta' => Arr::get($data, 'meta', $organizationUnit?->meta ?? []),
            'is_active' => (bool) Arr::get($data, 'is_active', $organizationUnit?->is_active ?? true),
            'sort_order' => (int) Arr::get($data, 'sort_order', $organizationUnit?->sort_order ?? 0),
        ];
    }

    private function ensureUniqueSlug(string $slug, string $type, ?OrganizationUnit $ignore = null): void
    {
        $query = OrganizationUnit::query()
            ->where('type', $type)
            ->where('slug', $slug);

        if ($ignore !== null) {
            $query->whereKeyNot($ignore->getKey());
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'slug' => 'Slug sudah digunakan pada jenis organisasi ini.',
            ]);
        }
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
