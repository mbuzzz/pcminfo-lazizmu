<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\LeaderOrganization;
use App\Filament\Resources\LeaderResource\Pages;
use App\Filament\Support\EnumOptions;
use App\Models\Institution;
use App\Models\Leader;
use App\Models\User;
use App\Services\Media\MediaUploadService;
use App\Support\Media\MediaPath;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class LeaderResource extends Resource
{
    protected static ?string $model = Leader::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Organisasi';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Struktur Pimpinan';

    protected static ?string $pluralModelLabel = 'Struktur Pimpinan';

    protected static ?string $navigationLabel = 'Struktur Pimpinan';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('user_id')
                ->label('Tautkan Pengguna')
                ->options(User::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('institution_id')
                ->label('Amal Usaha')
                ->options(Institution::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->preload(),
            Forms\Components\FileUpload::make('photo')
                ->label('Foto')
                ->image()
                ->disk(config('media.disk'))
                ->directory(MediaPath::leaderPhoto())
                ->visibility(config('media.visibility'))
                ->acceptedFileTypes(config('media.accepted_image_types'))
                ->maxSize((int) config('media.max_sizes_kb.image'))
                ->getUploadedFileNameForStorageUsing(fn (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file) => app(MediaUploadService::class)->generateFilename($file, 'leader-photo'))
                ->imageEditor()
                ->avatar()
                ->openable()
                ->downloadable(),
            Forms\Components\TextInput::make('name')
                ->label('Nama Lengkap')
                ->required(),
            Forms\Components\TextInput::make('position')
                ->label('Jabatan')
                ->required(),
            Forms\Components\TextInput::make('division')
                ->label('Divisi'),
            Forms\Components\TextInput::make('nbm')
                ->label('NBM'),
            Forms\Components\Select::make('organization')
                ->label('Organisasi')
                ->options(EnumOptions::make(LeaderOrganization::class))
                ->required(),
            Forms\Components\Select::make('position_level')
                ->label('Level Jabatan')
                ->options([
                    'leadership' => 'Pimpinan',
                    'vice' => 'Wakil',
                    'secretary' => 'Sekretaris',
                    'treasurer' => 'Bendahara',
                    'member' => 'Anggota',
                ])
                ->required(),
            Forms\Components\TextInput::make('period')
                ->label('Periode')
                ->placeholder('2022-2027')
                ->required(),
            Forms\Components\TextInput::make('phone')
                ->label('Telepon'),
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email(),
            Forms\Components\Textarea::make('bio')
                ->label('Biografi Singkat')
                ->rows(4),
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'active' => 'Aktif',
                    'inactive' => 'Nonaktif',
                ])
                ->required(),
            Forms\Components\TextInput::make('order')
                ->label('Urutan')
                ->numeric()
                ->default(0),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('period', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->description(fn (Leader $record): string => $record->position),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->disk(config('media.disk'))
                    ->circular(),
                Tables\Columns\TextColumn::make('organization')
                    ->label('Organisasi')
                    ->badge(),
                Tables\Columns\TextColumn::make('institution.name')
                    ->label('Amal Usaha')
                    ->placeholder('PCM / PCA'),
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('organization')
                    ->options(EnumOptions::make(LeaderOrganization::class)),
                Tables\Filters\SelectFilter::make('institution_id')
                    ->label('Amal Usaha')
                    ->options(Institution::query()->orderBy('name')->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                    ]),
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
            'index' => Pages\ListLeaders::route('/'),
            'create' => Pages\CreateLeader::route('/create'),
            'edit' => Pages\EditLeader::route('/{record}/edit'),
        ];
    }
}
