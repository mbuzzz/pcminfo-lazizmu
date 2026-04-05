<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Filament\Resources\CampaignResource\Pages;
use App\Filament\Resources\Concerns\HasResourceAuthorization;
use App\Filament\Support\EnumOptions;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Institution;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

class CampaignResource extends Resource
{
    use HasResourceAuthorization;

    protected static ?string $model = Campaign::class;

    protected static string $permission = 'manage_campaigns';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static string|UnitEnum|null $navigationGroup = 'Lazismu';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Program Donasi';

    protected static ?string $pluralModelLabel = 'Program Donasi';

    protected static ?string $navigationLabel = 'Program Donasi';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Schemas\Components\Tabs::make('CampaignTabs')
                ->tabs([
                    \Filament\Schemas\Components\Tabs\Tab::make('Informasi Utama')
                        ->schema([
                            \Filament\Schemas\Components\Grid::make(12)
                                ->schema([
                                    Forms\Components\TextInput::make('title')
                                        ->label('Judul Program')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn ($state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug((string) $state)))
                                        ->columnSpan(8),
                                    Forms\Components\TextInput::make('slug')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->columnSpan(4),
                                    Forms\Components\Select::make('category_id')
                                        ->label('Kategori')
                                        ->options(Category::query()->where('type', 'campaign')->orderBy('name')->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->columnSpan(4),
                                    Forms\Components\Select::make('institution_id')
                                        ->label('Amal Usaha')
                                        ->options(Institution::query()->orderBy('name')->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->columnSpan(4),
                                    Forms\Components\Select::make('type')
                                        ->options(EnumOptions::make(CampaignType::class))
                                        ->required()
                                        ->native(false)
                                        ->columnSpan(2),
                                    Forms\Components\Select::make('status')
                                        ->options(EnumOptions::make(CampaignStatus::class))
                                        ->required()
                                        ->native(false)
                                        ->columnSpan(2),
                                    Forms\Components\Textarea::make('short_description')
                                        ->label('Ringkasan Singkat')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    Forms\Components\RichEditor::make('description')
                                        ->label('Deskripsi Lengkap')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                    \Filament\Schemas\Components\Tabs\Tab::make('Target & Progress')
                        ->schema([
                            \Filament\Schemas\Components\Grid::make(12)
                                ->schema([
                                    Forms\Components\Select::make('progress_type')
                                        ->label('Tipe Progress')
                                        ->options([
                                            'amount' => 'Nominal (Rupiah)',
                                            'unit' => 'Unit',
                                        ])
                                        ->live()
                                        ->required()
                                        ->native(false)
                                        ->columnSpan(4),
                                    Forms\Components\TextInput::make('target_amount')
                                        ->label('Target Nominal')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => $get('progress_type') === 'amount')
                                        ->columnSpan(4),
                                    Forms\Components\TextInput::make('target_unit')
                                        ->label('Target Unit')
                                        ->numeric()
                                        ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => $get('progress_type') === 'unit')
                                        ->columnSpan(4),
                                    Forms\Components\TextInput::make('unit_label')
                                        ->label('Label Unit')
                                        ->placeholder('contoh: ekor, paket, meter')
                                        ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => $get('progress_type') === 'unit')
                                        ->columnSpan(4),
                                    Forms\Components\DatePicker::make('start_date')
                                        ->label('Tanggal Mulai')
                                        ->columnSpan(4),
                                    Forms\Components\DatePicker::make('end_date')
                                        ->label('Tanggal Berakhir')
                                        ->columnSpan(4),
                                    Forms\Components\Placeholder::make('progress_summary')
                                        ->label('Ringkasan Progress')
                                        ->content(fn (?Campaign $record): string => $record
                                            ? sprintf(
                                                'Terkumpul Rp %s | Unit %s | Donatur %s | Progress %s%%',
                                                number_format((int) $record->collected_amount, 0, ',', '.'),
                                                number_format((int) $record->collected_unit, 0, ',', '.'),
                                                number_format((int) $record->verified_donor_count, 0, ',', '.'),
                                                number_format((float) $record->progress_percentage, 1, ',', '.'),
                                            )
                                            : 'Progress akan tampil setelah campaign tersimpan.')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                    \Filament\Schemas\Components\Tabs\Tab::make('Builder Donasi')
                        ->schema([
                            \Filament\Schemas\Components\Section::make('Perilaku Donasi')
                                ->description('Atur opsi form donasi tanpa mengedit JSON manual.')
                                ->schema([
                                    Forms\Components\Toggle::make('allow_anonymous')
                                        ->label('Izinkan Donasi Anonim')
                                        ->default(true),
                                    Forms\Components\Toggle::make('show_donor_list')
                                        ->label('Tampilkan Nama Donatur di Publik')
                                        ->default(true),
                                    Forms\Components\KeyValue::make('payment_config')
                                        ->label('Konfigurasi Pembayaran')
                                        ->keyLabel('Kunci')
                                        ->valueLabel('Nilai')
                                        ->columnSpanFull(),
                                ]),
                            \Filament\Schemas\Components\Section::make('Builder Field Form')
                                ->description('Field ini disimpan ke `config.form.fields` agar admin non-teknis tetap bisa mengatur form campaign.')
                                ->schema([
                                    Forms\Components\Hidden::make('config.version')
                                        ->default(1),
                                    Forms\Components\TextInput::make('config.type')
                                        ->label('Tipe Konfigurasi')
                                        ->helperText('Samakan dengan tipe program bila membutuhkan pengaturan khusus.')
                                        ->default('general'),
                                    Forms\Components\Repeater::make('config.form.fields')
                                        ->label('Field Form Donasi')
                                        ->collapsed()
                                        ->cloneable()
                                        ->reorderable()
                                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['name'] ?? 'Kolom')
                                        ->schema([
                                            \Filament\Schemas\Components\Grid::make(12)
                                                ->schema([
                                                    Forms\Components\TextInput::make('name')
                                                        ->label('Nama Sistem')
                                                        ->required()
                                                        ->placeholder('payer_name')
                                                        ->columnSpan(3),
                                                    Forms\Components\TextInput::make('label')
                                                        ->label('Label Tampil')
                                                        ->required()
                                                        ->columnSpan(3),
                                                    Forms\Components\Select::make('type')
                                                        ->label('Tipe Input')
                                                        ->options([
                                                            'text' => 'Teks',
                                                            'textarea' => 'Area Teks',
                                                            'currency' => 'Nominal',
                                                            'number' => 'Angka',
                                                            'select' => 'Pilihan',
                                                            'unit_option' => 'Pilihan Unit',
                                                            'toggle' => 'Toggle',
                                                        ])
                                                        ->required()
                                                        ->columnSpan(2),
                                                    Forms\Components\Toggle::make('required')
                                                        ->label('Wajib')
                                                        ->inline(false)
                                                        ->columnSpan(2),
                                                    Forms\Components\TagsInput::make('rules')
                                                        ->label('Aturan Validasi')
                                                        ->placeholder('integer, min:10000')
                                                        ->columnSpan(2),
                                                    Forms\Components\KeyValue::make('options')
                                                        ->label('Pilihan Opsi')
                                                        ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => in_array($get('type'), ['select', 'unit_option'], true))
                                                        ->columnSpanFull(),
                                                ]),
                                        ])
                                        ->default([
                                            [
                                                'name' => 'payer_name',
                                                'label' => 'Nama Donatur',
                                                'type' => 'text',
                                                'required' => true,
                                                'rules' => ['string', 'max:100'],
                                            ],
                                            [
                                                'name' => 'amount',
                                                'label' => 'Nominal Donasi',
                                                'type' => 'currency',
                                                'required' => true,
                                                'rules' => ['integer', 'min:10000'],
                                            ],
                                        ])
                                        ->columnSpanFull(),
                                    Forms\Components\KeyValue::make('config.behavior')
                                        ->label('Override Perilaku')
                                        ->keyLabel('Kunci Perilaku')
                                        ->valueLabel('Nilai')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                    \Filament\Schemas\Components\Tabs\Tab::make('Publikasi')
                        ->schema([
                            Forms\Components\TextInput::make('beneficiary_name')
                                ->label('Nama Penerima Manfaat'),
                            Forms\Components\Textarea::make('beneficiary_description')
                                ->label('Deskripsi Penerima Manfaat')
                                ->rows(3),
                            Forms\Components\TextInput::make('meta_title')
                                ->label('Meta Title'),
                            Forms\Components\Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->rows(3),
                            Forms\Components\Toggle::make('is_featured')
                                ->label('Tampilkan sebagai Program Unggulan'),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Program')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Campaign $record): string => $record->institution?->name ?? 'Tanpa amal usaha'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('progress_type')
                    ->label('Progress')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'unit' ? 'warning' : 'success'),
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Tercapai')
                    ->suffix('%'),
                Tables\Columns\TextColumn::make('collected_amount')
                    ->label('Terkumpul')
                    ->money('IDR', divideBy: 1)
                    ->sortable(),
                Tables\Columns\TextColumn::make('verified_donor_count')
                    ->label('Donatur')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date('d M Y')
                    ->placeholder('Tanpa batas')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(EnumOptions::make(CampaignType::class)),
                Tables\Filters\SelectFilter::make('status')
                    ->options(EnumOptions::make(CampaignStatus::class)),
                Tables\Filters\SelectFilter::make('institution_id')
                    ->label('Amal Usaha')
                    ->options(Institution::query()->orderBy('name')->pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Unggulan'),
                Filter::make('expired')
                    ->label('Sudah berakhir')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('end_date')->whereDate('end_date', '<', now())),
            ])
            ->actions([
                ActionGroup::make([
                    \Filament\Actions\EditAction::make()
                        ->label('Ubah'),
                    \Filament\Actions\Action::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Campaign $record): bool => $record->status !== CampaignStatus::Active)
                        ->action(fn (Campaign $record) => $record->update(['status' => CampaignStatus::Active])),
                    \Filament\Actions\Action::make('pause')
                        ->label('Jeda')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn (Campaign $record): bool => $record->status === CampaignStatus::Active)
                        ->action(fn (Campaign $record) => $record->update(['status' => CampaignStatus::Paused])),
                    \Filament\Actions\Action::make('complete')
                        ->label('Selesai')
                        ->icon('heroicon-o-check-badge')
                        ->color('info')
                        ->requiresConfirmation()
                        ->visible(fn (Campaign $record): bool => $record->status !== CampaignStatus::Completed)
                        ->action(fn (Campaign $record) => $record->update(['status' => CampaignStatus::Completed])),
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
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}
