<?php

declare(strict_types=1);

namespace App\Filament\Resources\DistributionResource\Pages;

use App\Filament\Resources\DistributionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDistribution extends CreateRecord
{
    protected static string $resource = DistributionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
