<?php

declare(strict_types=1);

namespace App\Filament\Resources\AgencyResource\Pages;

use App\Domain\Organization\Services\OrganizationUnitService;
use App\Filament\Resources\AgencyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAgency extends EditRecord
{
    protected static string $resource = AgencyResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(OrganizationUnitService::class)->update($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus'),
        ];
    }

    protected function deleteRecord(): void
    {
        app(OrganizationUnitService::class)->delete($this->getRecord());
    }
}
