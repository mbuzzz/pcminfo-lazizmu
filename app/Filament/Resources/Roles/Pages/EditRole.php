<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Pages;

use App\Domain\Access\Services\RoleService;
use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions'] = $this->getRecord()->permissions()->pluck('permissions.name')->all();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(RoleService::class)->update($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus'),
        ];
    }

    protected function deleteRecord(): void
    {
        app(RoleService::class)->delete($this->getRecord());
    }
}
