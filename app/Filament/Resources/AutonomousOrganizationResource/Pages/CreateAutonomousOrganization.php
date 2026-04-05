<?php

declare(strict_types=1);

namespace App\Filament\Resources\AutonomousOrganizationResource\Pages;

use App\Domain\Organization\Services\OrganizationUnitService;
use App\Enums\OrganizationUnitType;
use App\Filament\Resources\AutonomousOrganizationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAutonomousOrganization extends CreateRecord
{
    protected static string $resource = AutonomousOrganizationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(OrganizationUnitService::class)->create($data, OrganizationUnitType::AutonomousOrganization);
    }
}
