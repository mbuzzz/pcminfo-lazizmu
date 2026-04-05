<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domain\Content\Services\AgendaService;
use App\Enums\AgendaStatus;
use App\Enums\AgendaType;
use App\Filament\Resources\AgendaResource\Pages;
use App\Filament\Resources\Concerns\HasResourceAuthorization;
use App\Filament\Support\EnumOptions;
use App\Models\Agenda;
use App\Models\Category;
use App\Models\Institution;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class AgendaResource extends Resource
{
    use HasResourceAuthorization;

    protected static ?string $model = Agenda::class;

    protected static string $permission = 'manage_agendas';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Agenda';

    protected static ?string $pluralModelLabel = 'Agenda';

    protected static ?string $navigationLabel = 'Agenda';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('title')
                ->label('Judul Agenda')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug((string) $state))),
            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true),
            \Filament\Schemas\Components\Grid::make(12)
                ->schema([
                    Forms\Components\Select::make('category_id')
                        ->label('Kategori')
                        ->options(Category::query()->where('type', 'agenda')->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->columnSpan(3),
                    Forms\Components\Select::make('institution_id')
                        ->label('Amal Usaha')
                        ->options(Institution::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->columnSpan(3),
                    Forms\Components\Select::make('type')
                        ->label('Jenis Agenda')
                        ->options(EnumOptions::make(AgendaType::class))
                        ->required()
                        ->columnSpan(3),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options(EnumOptions::make(AgendaStatus::class))
                        ->required()
                        ->columnSpan(3),
                    Forms\Components\DateTimePicker::make('start_at')
                        ->label('Mulai')
                        ->required()
                        ->columnSpan(6),
                    Forms\Components\DateTimePicker::make('end_at')
                        ->label('Selesai')
                        ->columnSpan(6),
                ]),
            Forms\Components\Textarea::make('description')
                ->label('Deskripsi')
                ->rows(4),
            \Filament\Schemas\Components\Section::make('Lokasi')
                ->schema([
                    Forms\Components\TextInput::make('location_name')
                        ->label('Nama Lokasi'),
                    Forms\Components\TextInput::make('location_address')
                        ->label('Alamat Lokasi'),
                    Forms\Components\TextInput::make('maps_url')
                        ->label('URL Google Maps'),
                    Forms\Components\Toggle::make('is_online')
                        ->label('Agenda Daring')
                        ->live(),
                    Forms\Components\TextInput::make('meeting_url')
                        ->label('URL Meeting')
                        ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => (bool) $get('is_online')),
                ])
                ->columns(2),
            \Filament\Schemas\Components\Section::make('Registrasi')
                ->schema([
                    Forms\Components\Toggle::make('requires_registration')
                        ->label('Perlu Pendaftaran')
                        ->live(),
                    Forms\Components\TextInput::make('max_participants')
                        ->label('Maksimal Peserta')
                        ->numeric()
                        ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => (bool) $get('requires_registration')),
                    Forms\Components\Toggle::make('is_recurring')
                        ->label('Agenda Berulang')
                        ->live(),
                    Forms\Components\TextInput::make('recurrence_rule')
                        ->label('Aturan Pengulangan')
                        ->placeholder('FREQ=WEEKLY;BYDAY=SU')
                        ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => (bool) $get('is_recurring')),
                    Forms\Components\TextInput::make('contact_name')
                        ->label('Nama Kontak'),
                    Forms\Components\TextInput::make('contact_phone')
                        ->label('Telepon Kontak'),
                    Forms\Components\Toggle::make('is_featured')
                        ->label('Agenda Unggulan'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Agenda')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Agenda $record): string => $record->institution?->name ?? 'Agenda PCM'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('start_at')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registered_count')
                    ->label('Pendaftar')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(EnumOptions::make(AgendaType::class)),
                Tables\Filters\SelectFilter::make('status')
                    ->options(EnumOptions::make(AgendaStatus::class)),
                Tables\Filters\SelectFilter::make('institution_id')
                    ->label('Amal Usaha')
                    ->options(Institution::query()->orderBy('name')->pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('requires_registration'),
            ])
            ->actions([
                ActionGroup::make([
                    \Filament\Actions\EditAction::make()
                        ->label('Ubah'),
                    \Filament\Actions\Action::make('publish')
                        ->label('Publikasikan')
                        ->color('success')
                        ->visible(fn (Agenda $record): bool => $record->status === AgendaStatus::Draft)
                        ->action(fn (Agenda $record) => app(AgendaService::class)->publish($record)),
                    \Filament\Actions\Action::make('complete')
                        ->label('Selesaikan')
                        ->color('info')
                        ->visible(fn (Agenda $record): bool => $record->status === AgendaStatus::Published)
                        ->action(fn (Agenda $record) => app(AgendaService::class)->complete($record)),
                ]),
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
            'index' => Pages\ListAgendas::route('/'),
            'create' => Pages\CreateAgenda::route('/create'),
            'edit' => Pages\EditAgenda::route('/{record}/edit'),
        ];
    }
}
