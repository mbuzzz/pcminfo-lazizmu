<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Domain\Access\Services\UserService;
use App\Filament\Resources\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['roles'] = $this->getRecord()->roles()->pluck('roles.id')->all();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(UserService::class)->update($record, $data, auth()->user());
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Nonaktifkan/Hapus'),
        ];
    }

    protected function deleteRecord(): void
    {
        app(UserService::class)->delete($this->getRecord(), auth()->user());
    }
}
