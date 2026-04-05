<?php

declare(strict_types=1);

namespace App\Filament\Resources\InstitutionResource\Pages;

use App\Domain\Organization\Services\InstitutionService;
use App\Filament\Resources\InstitutionResource;
use App\Models\Institution;
use Filament\Resources\Pages\CreateRecord;

class CreateInstitution extends CreateRecord
{
    protected static string $resource = InstitutionResource::class;

    protected function handleRecordCreation(array $data): Institution
    {
        return app(InstitutionService::class)->create($data);
    }
}
