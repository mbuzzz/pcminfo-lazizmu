<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Role')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('Jumlah Pengguna')
                    ->sortable(),
                TextColumn::make('permissions_count')
                    ->label('Jumlah Permission')
                    ->sortable(),
                IconColumn::make('is_core_role')
                    ->label('Role Inti')
                    ->boolean()
                    ->state(fn ($record): bool => $record->isCoreRole()),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang dipilih')
                        ->authorizeIndividualRecords(),
                ]),
            ]);
    }
}
