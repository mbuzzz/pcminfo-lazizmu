<?php

declare(strict_types=1);

namespace App\Filament\Resources\LeaderResource\Pages;

use App\Domain\Organization\Services\LeaderService;
use App\Filament\Resources\LeaderResource;
use App\Models\Leader;
use Filament\Resources\Pages\EditRecord;

class EditLeader extends EditRecord
{
    protected static string $resource = LeaderResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): Leader
    {
        return app(LeaderService::class)->update($record, $data);
    }
}
