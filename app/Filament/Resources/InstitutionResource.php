<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\InstitutionType;
use App\Filament\Resources\InstitutionResource\Pages;
use App\Filament\Support\EnumOptions;
use App\Models\Institution;
use App\Services\Media\MediaUploadService;
use App\Support\Media\MediaPath;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class InstitutionResource extends Resource
{
    protected static ?string $model = Institution::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static string|UnitEnum|null $navigationGroup = 'Organisasi';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Amal Usaha';

    protected static ?string $pluralModelLabel = 'Amal Usaha';

    protected static ?string $navigationLabel = 'Amal Usaha';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Amal Usaha')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug((string) $state))),
            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('acronym')
                ->label('Singkatan'),
            Forms\Components\TextInput::make('tagline')
                ->label('Tagline'),
            Forms\Components\FileUpload::make('logo')
                ->label('Logo')
                ->image()
                ->disk(config('media.disk'))
                ->directory(MediaPath::institutionLogo())
                ->visibility(config('media.visibility'))
                ->acceptedFileTypes(config('media.accepted_image_types'))
                ->maxSize((int) config('media.max_sizes_kb.image'))
                ->getUploadedFileNameForStorageUsing(fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file) => app(MediaUploadService::class)->generateFilename($file, 'institution-logo'))
                ->imageEditor()
                ->openable()
                ->downloadable(),
            Forms\Components\FileUpload::make('cover_image')
                ->label('Gambar Sampul')
                ->image()
                ->disk(config('media.disk'))
                ->directory(MediaPath::institutionCover())
                ->visibility(config('media.visibility'))
                ->acceptedFileTypes(config('media.accepted_image_types'))
                ->maxSize((int) config('media.max_sizes_kb.image'))
                ->getUploadedFileNameForStorageUsing(fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file) => app(MediaUploadService::class)->generateFilename($file, 'institution-cover'))
                ->imageEditor()
                ->openable()
                ->downloadable(),
            Forms\Components\Textarea::make('description')
                ->label('Deskripsi')
                ->rows(4)
                ->columnSpanFull(),
            \Filament\Schemas\Components\Grid::make(12)
                ->schema([
                    Forms\Components\Select::make('type')
                        ->label('Jenis Amal Usaha')
                        ->options(EnumOptions::make(InstitutionType::class))
                        ->required()
                        ->columnSpan(4),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'active' => 'Aktif',
                            'inactive' => 'Nonaktif',
                            'development' => 'Pengembangan',
                        ])
                        ->required()
                        ->columnSpan(4),
                    Forms\Components\TextInput::make('founded_year')
                        ->label('Tahun Berdiri')
                        ->numeric()
                        ->columnSpan(4),
                ]),
            \Filament\Schemas\Components\Section::make('Kontak & Lokasi')
                ->schema([
                    Forms\Components\Textarea::make('address')
                        ->label('Alamat')
                        ->rows(3),
                    Forms\Components\TextInput::make('village')
                        ->label('Kelurahan/Desa'),
                    Forms\Components\TextInput::make('district')
                        ->label('Kecamatan'),
                    Forms\Components\TextInput::make('city')
                        ->label('Kota/Kabupaten'),
                    Forms\Components\TextInput::make('province')
                        ->label('Provinsi'),
                    Forms\Components\TextInput::make('postal_code')
                        ->label('Kode Pos'),
                    Forms\Components\TextInput::make('phone')
                        ->label('Telepon'),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email(),
                    Forms\Components\TextInput::make('website')
                        ->label('Website')
                        ->url(),
                    Forms\Components\TextInput::make('latitude')
                        ->label('Latitude')
                        ->numeric(),
                    Forms\Components\TextInput::make('longitude')
                        ->label('Longitude')
                        ->numeric(),
                ])
                ->columns(2),
            \Filament\Schemas\Components\Section::make('Opsi')
                ->schema([
                    Forms\Components\TextInput::make('accreditation')
                        ->label('Akreditasi'),
                    Forms\Components\TextInput::make('order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(0),
                    Forms\Components\Toggle::make('is_featured')
                        ->label('Unggulan'),
                    Forms\Components\KeyValue::make('meta')
                        ->label('Metadata Tambahan')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Institution $record): string => $record->city ?: 'Tanpa kota'),
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk(config('media.disk'))
                    ->square(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('founded_year')
                    ->label('Tahun Berdiri')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(EnumOptions::make(InstitutionType::class)),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                        'development' => 'Pengembangan',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->label('Ubah'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->label('Hapus yang dipilih'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstitutions::route('/'),
            'create' => Pages\CreateInstitution::route('/create'),
            'edit' => Pages\EditInstitution::route('/{record}/edit'),
        ];
    }
}
