<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Domain\Access\Services\UserService;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(UserService::class)->create($data, auth()->user());
    }
}
