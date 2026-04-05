<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Enums\AgendaStatus;
use App\Models\Agenda;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final class AgendaService
{
    public function create(array $data): Agenda
    {
        $data = $this->normalize($data);
        $data['created_by'] ??= auth()->id();
        $data['status'] ??= AgendaStatus::Draft;

        return Agenda::query()->create($data);
    }

    public function update(Agenda $agenda, array $data): Agenda
    {
        $agenda->update($this->normalize($data, $agenda));

        return $agenda->refresh();
    }

    public function publish(Agenda $agenda): Agenda
    {
        $agenda->update([
            'status' => AgendaStatus::Published,
        ]);

        return $agenda->refresh();
    }

    public function complete(Agenda $agenda): Agenda
    {
        $agenda->update([
            'status' => AgendaStatus::Completed,
        ]);

        return $agenda->refresh();
    }

    private function normalize(array $data, ?Agenda $agenda = null): array
    {
        $slug = filled(Arr::get($data, 'slug'))
            ? Str::slug((string) $data['slug'])
            : Str::slug((string) Arr::get($data, 'title', $agenda?->title));

        $data['slug'] = $this->makeUniqueSlug($slug, $agenda);

        return $data;
    }

    private function makeUniqueSlug(string $slug, ?Agenda $agenda = null): string
    {
        $baseSlug = $slug !== '' ? $slug : 'agenda';
        $candidate = $baseSlug;
        $iteration = 2;

        while (
            Agenda::query()
                ->when($agenda !== null, fn ($query) => $query->whereKeyNot($agenda->getKey()))
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = "{$baseSlug}-{$iteration}";
            $iteration++;
        }

        return $candidate;
    }
}
