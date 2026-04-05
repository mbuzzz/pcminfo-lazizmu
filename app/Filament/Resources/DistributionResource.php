<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\DistributionStatus;
use App\Filament\Resources\Concerns\HasResourceAuthorization;
use App\Filament\Resources\DistributionResource\Pages;
use App\Filament\Support\EnumOptions;
use App\Models\Campaign;
use App\Models\Distribution;
use App\Models\Institution;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class DistributionResource extends Resource
{
    use HasResourceAuthorization;

    protected static ?string $model = Distribution::class;

    protected static string $permission = 'manage_distribution_reports';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static string|UnitEnum|null $navigationGroup = 'Lazismu';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Laporan Penyaluran';

    protected static ?string $pluralModelLabel = 'Laporan Penyaluran';

    protected static ?string $navigationLabel = 'Laporan Penyaluran';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Schemas\Components\Section::make('Sumber Penyaluran')
                ->schema([
                    Forms\Components\Select::make('campaign_id')
                        ->label('Program Donasi')
                        ->options(Campaign::query()->orderBy('title')->pluck('title', 'id'))
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('institution_id')
                        ->label('Amal Usaha')
                        ->options(Institution::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('distribution_code')
                        ->label('Kode Penyaluran')
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Laporan')
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            \Filament\Schemas\Components\Section::make('Penerima & Nilai')
                ->schema([
                    Forms\Components\Select::make('recipient_type')
                        ->label('Jenis Penerima')
                        ->options([
                            'fakir' => 'Fakir',
                            'miskin' => 'Miskin',
                            'amil' => 'Amil',
                            'muallaf' => 'Muallaf',
                            'riqab' => 'Riqab',
                            'gharimin' => 'Gharimin',
                            'fisabilillah' => 'Fisabilillah',
                            'ibnu_sabil' => 'Ibnu Sabil',
                            'general' => 'Umum',
                            'institution' => 'Institusi',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('recipient_name')
                        ->label('Nama Penerima'),
                    Forms\Components\TextInput::make('recipient_count')
                        ->label('Jumlah Penerima')
                        ->numeric()
                        ->default(1),
                    Forms\Components\TextInput::make('distributed_amount')
                        ->label('Nominal Tersalurkan')
                        ->numeric()
                        ->prefix('Rp'),
                    Forms\Components\TextInput::make('distributed_unit')
                        ->label('Unit Tersalurkan')
                        ->numeric(),
                    Forms\Components\TextInput::make('unit_label')
                        ->label('Label Unit'),
                    Forms\Components\Select::make('distribution_type')
                        ->label('Tipe Penyaluran')
                        ->options([
                            'cash' => 'Tunai',
                            'goods' => 'Barang',
                            'service' => 'Layanan',
                            'mixed' => 'Campuran',
                        ])
                        ->required(),
                ])
                ->columns(3),
            \Filament\Schemas\Components\Section::make('Pelaksanaan')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options(EnumOptions::make(DistributionStatus::class))
                        ->required(),
                    Forms\Components\DatePicker::make('distribution_date')
                        ->label('Tanggal Penyaluran'),
                    Forms\Components\TextInput::make('location')
                        ->label('Lokasi'),
                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(3)
                        ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('distribution_code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->description(fn (Distribution $record): string => $record->campaign?->title ?? 'Kas umum'),
                Tables\Columns\TextColumn::make('recipient_type')
                    ->label('Jenis Penerima')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('distributed_amount')
                    ->label('Nominal')
                    ->money('IDR', divideBy: 1)
                    ->sortable(),
                Tables\Columns\TextColumn::make('recipient_count')
                    ->label('Penerima')
                    ->sortable(),
                Tables\Columns\TextColumn::make('distribution_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(EnumOptions::make(DistributionStatus::class)),
                Tables\Filters\SelectFilter::make('campaign_id')
                    ->label('Program Donasi')
                    ->options(Campaign::query()->orderBy('title')->pluck('title', 'id')),
                Tables\Filters\SelectFilter::make('institution_id')
                    ->label('Amal Usaha')
                    ->options(Institution::query()->orderBy('name')->pluck('name', 'id')),
            ])
            ->actions([
                ActionGroup::make([
                    \Filament\Actions\EditAction::make()
                        ->label('Ubah'),
                    \Filament\Actions\Action::make('approve')
                        ->label('Setujui')
                        ->color('warning')
                        ->visible(fn (Distribution $record): bool => $record->status === DistributionStatus::Draft)
                        ->action(fn (Distribution $record) => $record->update([
                            'status' => DistributionStatus::Approved,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ])),
                    \Filament\Actions\Action::make('markDistributed')
                        ->label('Tandai Tersalurkan')
                        ->color('success')
                        ->visible(fn (Distribution $record): bool => $record->status === DistributionStatus::Approved)
                        ->action(fn (Distribution $record) => $record->update([
                            'status' => DistributionStatus::Distributed,
                            'distributed_by' => auth()->id(),
                            'distribution_date' => $record->distribution_date ?? now()->toDateString(),
                        ])),
                    \Filament\Actions\Action::make('markReported')
                        ->label('Tandai Laporan Lengkap')
                        ->color('info')
                        ->visible(fn (Distribution $record): bool => $record->status === DistributionStatus::Distributed)
                        ->action(fn (Distribution $record) => $record->update([
                            'status' => DistributionStatus::Reported,
                        ])),
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
            'index' => Pages\ListDistributions::route('/'),
            'create' => Pages\CreateDistribution::route('/create'),
            'edit' => Pages\EditDistribution::route('/{record}/edit'),
        ];
    }
}
