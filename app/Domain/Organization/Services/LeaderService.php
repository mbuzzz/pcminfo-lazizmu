<?php

declare(strict_types=1);

namespace App\Domain\Organization\Services;

use App\Models\Leader;
use App\Services\Media\MediaUploadService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final class LeaderService
{
    public function __construct(
        private readonly MediaUploadService $mediaUploadService,
    ) {
    }

    public function create(array $data): Leader
    {
        return Leader::query()->create($this->normalize($data));
    }

    public function update(Leader $leader, array $data): Leader
    {
        return DB::transaction(function () use ($leader, $data): Leader {
            $photoWasSubmitted = array_key_exists('photo', $data);
            $payload = $this->normalize($data, $leader);

            $this->mediaUploadService->sync($leader->photo, $payload['photo'] ?? $leader->photo, $photoWasSubmitted);

            $leader->update($payload);

            return $leader->refresh();
        });
    }

    private function normalize(array $data, ?Leader $leader = null): array
    {
        $data['order'] = (int) Arr::get($data, 'order', $leader?->order ?? 0);

        return $data;
    }
}
