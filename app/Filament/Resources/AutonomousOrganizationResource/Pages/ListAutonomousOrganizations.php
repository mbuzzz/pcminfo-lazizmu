<?php

declare(strict_types=1);

namespace App\Filament\Resources\AutonomousOrganizationResource\Pages;

use App\Filament\Resources\AutonomousOrganizationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAutonomousOrganizations extends ListRecords
{
    protected static string $resource = AutonomousOrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Organisasi Otonom'),
        ];
    }
}
