<?php

declare(strict_types=1);

namespace App\Filament\Resources\LeaderResource\Pages;

use App\Domain\Organization\Services\LeaderService;
use App\Filament\Resources\LeaderResource;
use App\Models\Leader;
use Filament\Resources\Pages\CreateRecord;

class CreateLeader extends CreateRecord
{
    protected static string $resource = LeaderResource::class;

    protected function handleRecordCreation(array $data): Leader
    {
        return app(LeaderService::class)->create($data);
    }
}
