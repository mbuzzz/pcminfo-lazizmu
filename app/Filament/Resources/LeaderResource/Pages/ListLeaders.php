<?php

declare(strict_types=1);

namespace App\Filament\Resources\LeaderResource\Pages;

use App\Filament\Resources\LeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaders extends ListRecords
{
    protected static string $resource = LeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Struktur Pimpinan'),
        ];
    }
}
