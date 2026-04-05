<?php

declare(strict_types=1);

namespace App\Filament\Resources\CouncilResource\Pages;

use App\Domain\Organization\Services\OrganizationUnitService;
use App\Enums\OrganizationUnitType;
use App\Filament\Resources\CouncilResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCouncil extends CreateRecord
{
    protected static string $resource = CouncilResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(OrganizationUnitService::class)->create($data, OrganizationUnitType::Council);
    }
}
