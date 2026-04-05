<?php

declare(strict_types=1);

namespace App\Filament\Resources\AgendaResource\Pages;

use App\Domain\Content\Services\AgendaService;
use App\Filament\Resources\AgendaResource;
use App\Models\Agenda;
use Filament\Resources\Pages\EditRecord;

class EditAgenda extends EditRecord
{
    protected static string $resource = AgendaResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): Agenda
    {
        return app(AgendaService::class)->update($record, $data);
    }
}
