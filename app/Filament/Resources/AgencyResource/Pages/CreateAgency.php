<?php

declare(strict_types=1);

namespace App\Filament\Resources\AgencyResource\Pages;

use App\Domain\Organization\Services\OrganizationUnitService;
use App\Enums\OrganizationUnitType;
use App\Filament\Resources\AgencyResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAgency extends CreateRecord
{
    protected static string $resource = AgencyResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(OrganizationUnitService::class)->create($data, OrganizationUnitType::Agency);
    }
}
