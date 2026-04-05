<?php

declare(strict_types=1);

namespace App\Filament\Resources\OrganizationUnits;

use App\Enums\OrganizationUnitType;
use App\Models\OrganizationUnit;
use App\Services\Media\MediaUploadService;
use App\Support\Media\MediaPath;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

abstract class BaseOrganizationUnitResource extends Resource
{
    protected static ?string $model = OrganizationUnit::class;

    protected static string|UnitEnum|null $navigationGroup = 'Organisasi';

    abstract protected static function getUnitType(): OrganizationUnitType;

    abstract protected static function getPermissionName(): string;

    abstract protected static function getEntityLabel(): string;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Hidden::make('type')
                ->default(static::getUnitType()->value)
                ->dehydrated(),
            Forms\Components\TextInput::make('name')
                ->label('Nama')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (?string $state, \Filament\Schemas\Components\Utilities\Set $set): mixed => $set('slug', Str::slug((string) $state))),
            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('acronym')
                ->label('Singkatan')
                ->maxLength(30),
            Forms\Components\TextInput::make('tagline')
                ->label('Tagline')
                ->maxLength(255),
            Forms\Components\FileUpload::make('logo')
                ->label('Logo')
                ->image()
                ->disk(config('media.disk'))
                ->directory(MediaPath::organizationUnitLogo())
                ->visibility(config('media.visibility'))
                ->acceptedFileTypes(config('media.accepted_image_types'))
                ->maxSize((int) config('media.max_sizes_kb.image'))
                ->getUploadedFileNameForStorageUsing(
                    fn (TemporaryUploadedFile $file): string => app(MediaUploadService::class)->generateFilename($file, static::getUnitType()->value . '-logo')
                )
                ->openable()
                ->downloadable()
                ->imageEditor(),
            Forms\Components\Textarea::make('description')
                ->label('Deskripsi')
                ->rows(5)
                ->columnSpanFull(),
            Section::make('Kontak')
                ->schema([
                    Forms\Components\TextInput::make('chairperson')
                        ->label('Ketua'),
                    Forms\Components\TextInput::make('secretary')
                        ->label('Sekretaris'),
                    Forms\Components\TextInput::make('phone')
                        ->label('Telepon')
                        ->tel(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email(),
                    Forms\Components\TextInput::make('website')
                        ->label('Website')
                        ->url(),
                    Forms\Components\Textarea::make('address')
                        ->label('Alamat')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Pengaturan')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(0),
                    Forms\Components\KeyValue::make('meta')
                        ->label('Metadata Tambahan')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk(config('media.disk'))
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn (OrganizationUnit $record): string => $record->tagline ?: '-'),
                Tables\Columns\TextColumn::make('acronym')
                    ->label('Singkatan')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('chairperson')
                    ->label('Ketua')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->label('Ubah'),
                \Filament\Actions\DeleteAction::make()
                    ->label('Hapus'),
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
            ->where('type', static::getUnitType()->value);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()
            ->where('type', static::getUnitType()->value)
            ->count();
    }

    public static function getNavigationLabel(): string
    {
        return static::getEntityLabel();
    }

    public static function getModelLabel(): string
    {
        return static::getEntityLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return static::getEntityLabel();
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(static::getPermissionName()) ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can(static::getPermissionName()) ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can(static::getPermissionName()) ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }

    public static function canRestore($record): bool
    {
        return auth()->user()?->can(static::getPermissionName()) ?? false;
    }

    public static function canRestoreAny(): bool
    {
        return static::canViewAny();
    }
}
