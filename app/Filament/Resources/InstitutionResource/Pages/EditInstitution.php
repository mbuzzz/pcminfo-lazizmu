<?php

declare(strict_types=1);

namespace App\Filament\Resources\InstitutionResource\Pages;

use App\Domain\Organization\Services\InstitutionService;
use App\Filament\Resources\InstitutionResource;
use App\Models\Institution;
use Filament\Resources\Pages\EditRecord;

class EditInstitution extends EditRecord
{
    protected static string $resource = InstitutionResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): Institution
    {
        return app(InstitutionService::class)->update($record, $data);
    }
}
