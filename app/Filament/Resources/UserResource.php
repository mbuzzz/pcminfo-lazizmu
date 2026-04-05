<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Role;
use App\Models\User;
use App\Services\Media\MediaUploadService;
use App\Support\Media\MediaPath;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Pengguna';

    protected static ?string $pluralModelLabel = 'Pengguna';

    protected static ?string $navigationLabel = 'Pengguna';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\FileUpload::make('avatar')
                ->label('Avatar')
                ->image()
                ->avatar()
                ->disk(config('media.disk'))
                ->directory(MediaPath::userAvatar())
                ->visibility(config('media.visibility'))
                ->acceptedFileTypes(config('media.accepted_image_types'))
                ->maxSize((int) config('media.max_sizes_kb.image'))
                ->getUploadedFileNameForStorageUsing(
                    fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string => app(MediaUploadService::class)->generateFilename($file, 'user-avatar')
                ),
            Forms\Components\TextInput::make('name')
                ->label('Nama Lengkap')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->revealable()
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->required(fn (string $operation): bool => $operation === 'create')
                ->minLength(8)
                ->same('password_confirmation'),
            Forms\Components\TextInput::make('password_confirmation')
                ->label('Konfirmasi Password')
                ->password()
                ->revealable()
                ->dehydrated(false)
                ->required(fn (string $operation): bool => $operation === 'create'),
            Forms\Components\Select::make('roles')
                ->label('Peran')
                ->options(Role::query()->orderBy('name')->pluck('name', 'id'))
                ->multiple()
                ->preload()
                ->searchable(),
            Forms\Components\Toggle::make('is_active')
                ->label('Akun Aktif')
                ->default(true),
            Forms\Components\DateTimePicker::make('email_verified_at')
                ->label('Email Terverifikasi Pada')
                ->seconds(false),
            Forms\Components\DateTimePicker::make('last_login_at')
                ->label('Login Terakhir')
                ->seconds(false)
                ->disabled(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->disk(config('media.disk'))
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Peran')
                    ->badge()
                    ->separator(','),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email Terverifikasi')
                    ->boolean()
                    ->state(fn (User $record): bool => $record->email_verified_at !== null),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Login Terakhir')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Belum ada'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Peran')
                    ->relationship('roles', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email Terverifikasi')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query): Builder => $query->whereNull('email_verified_at'),
                        blank: fn (Builder $query): Builder => $query,
                    ),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->label('Ubah'),
                \Filament\Actions\DeleteAction::make()
                    ->label('Nonaktifkan/Hapus'),
                \Filament\Actions\RestoreAction::make()
                    ->label('Pulihkan'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->label('Hapus yang dipilih')
                        ->authorizeIndividualRecords(),
                    \Filament\Actions\RestoreBulkAction::make()
                        ->label('Pulihkan yang dipilih')
                        ->authorizeIndividualRecords(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->with('roles');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
