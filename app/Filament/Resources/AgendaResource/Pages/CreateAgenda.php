<?php

declare(strict_types=1);

namespace App\Filament\Resources\AgendaResource\Pages;

use App\Domain\Content\Services\AgendaService;
use App\Filament\Resources\AgendaResource;
use App\Models\Agenda;
use Filament\Resources\Pages\CreateRecord;

class CreateAgenda extends CreateRecord
{
    protected static string $resource = AgendaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function handleRecordCreation(array $data): Agenda
    {
        return app(AgendaService::class)->create($this->mutateFormDataBeforeCreate($data));
    }
}
