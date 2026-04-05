<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Schemas;

use App\Domain\Access\Services\PermissionMatrixService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Role')
                    ->required()
                    ->maxLength(255),
                CheckboxList::make('permissions')
                    ->label('Permission')
                    ->options(app(PermissionMatrixService::class)->options())
                    ->columns(2)
                    ->bulkToggleable()
                    ->columnSpanFull(),
            ])->columns(2);
    }
}
