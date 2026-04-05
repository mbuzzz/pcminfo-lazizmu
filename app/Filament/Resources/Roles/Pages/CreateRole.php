<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Pages;

use App\Domain\Access\Services\RoleService;
use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(RoleService::class)->create($data);
    }
}
