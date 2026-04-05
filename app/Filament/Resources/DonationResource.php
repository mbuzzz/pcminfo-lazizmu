<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\DonationPaymentMethod;
use App\Enums\DonationStatus;
use App\Filament\Resources\Concerns\HasResourceAuthorization;
use App\Filament\Resources\DonationResource\Pages;
use App\Filament\Support\EnumOptions;
use App\Models\Campaign;
use App\Models\Donation;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class DonationResource extends Resource
{
    use HasResourceAuthorization;

    protected static ?string $model = Donation::class;

    protected static string $permission = 'manage_donations';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|UnitEnum|null $navigationGroup = 'Lazismu';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Donasi';

    protected static ?string $pluralModelLabel = 'Donasi';

    protected static ?string $navigationLabel = 'Donasi';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            \Filament\Schemas\Components\Section::make('Informasi Donasi')
                ->schema([
                    Forms\Components\Select::make('campaign_id')
                        ->label('Program Donasi')
                        ->options(Campaign::query()->orderBy('title')->pluck('title', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('transaction_code')
                        ->label('Kode Transaksi')
                        ->required()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('payer_name')
                        ->label('Nama Donatur')
                        ->required(),
                    Forms\Components\TextInput::make('payer_email')
                        ->label('Email Donatur')
                        ->email(),
                    Forms\Components\TextInput::make('payer_phone')
                        ->label('Telepon Donatur'),
                    Forms\Components\Toggle::make('is_anonymous')
                        ->label('Sembunyikan nama donatur'),
                    Forms\Components\TextInput::make('amount')
                        ->label('Nominal Donasi')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                    Forms\Components\TextInput::make('quantity')
                        ->label('Jumlah Unit')
                        ->numeric(),
                    Forms\Components\TextInput::make('unit_label')
                        ->label('Label Unit'),
                    Forms\Components\Textarea::make('message')
                        ->label('Pesan Donatur')
                        ->rows(3),
                ])
                ->columns(2),
            \Filament\Schemas\Components\Section::make('Pembayaran & Verifikasi')
                ->schema([
                    Forms\Components\Select::make('payment_method')
                        ->label('Metode Pembayaran')
                        ->options(EnumOptions::make(DonationPaymentMethod::class))
                        ->required(),
                    Forms\Components\TextInput::make('payment_channel')
                        ->label('Kanal Pembayaran'),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options(EnumOptions::make(DonationStatus::class))
                        ->required(),
                    Forms\Components\DateTimePicker::make('submitted_at')
                        ->label('Tanggal Pengajuan'),
                    Forms\Components\DateTimePicker::make('verified_at')
                        ->label('Tanggal Verifikasi'),
                    Forms\Components\DateTimePicker::make('rejected_at')
                        ->label('Tanggal Penolakan'),
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
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('transaction_code')
                    ->label('Kode Transaksi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('campaign.title')
                    ->label('Program Donasi')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Donatur')
                    ->searchable(['payer_name', 'payer_email', 'payer_phone']),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR', divideBy: 1)
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode Bayar')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('verified_at')
                    ->label('Diverifikasi')
                    ->since()
                    ->placeholder('Belum diverifikasi'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('campaign_id')
                    ->label('Program Donasi')
                    ->options(Campaign::query()->orderBy('title')->pluck('title', 'id')),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options(EnumOptions::make(DonationPaymentMethod::class)),
                Tables\Filters\SelectFilter::make('status')
                    ->options(EnumOptions::make(DonationStatus::class)),
                Filter::make('manual_payment')
                    ->label('Perlu verifikasi manual')
                    ->query(fn (Builder $query): Builder => $query->whereIn('payment_method', [
                        DonationPaymentMethod::ManualTransfer->value,
                        DonationPaymentMethod::Cash->value,
                        DonationPaymentMethod::BankTransfer->value,
                    ])),
                Filter::make('this_month')
                    ->label('Donasi bulan ini')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)),
            ])
            ->actions([
                ActionGroup::make([
                    \Filament\Actions\EditAction::make()
                        ->label('Ubah'),
                    \Filament\Actions\Action::make('approve')
                        ->label('Verifikasi')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (Donation $record): bool => $record->status === DonationStatus::Pending && (auth()->user()?->can('verify_donations') ?? false))
                        ->requiresConfirmation()
                        ->action(function (Donation $record): void {
                            $record->update([
                                'status' => DonationStatus::Verified,
                                'verified_by' => auth()->id(),
                                'verified_at' => now(),
                                'rejected_at' => null,
                            ]);

                            if ($record->campaign) {
                                $record->campaign->increment('verified_donor_count');
                                $record->campaign->increment('collected_amount', (int) $record->amount);
                                $record->campaign->increment('collected_unit', (int) $record->quantity);
                            }
                        }),
                    \Filament\Actions\Action::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn (Donation $record): bool => $record->status === DonationStatus::Pending && (auth()->user()?->can('verify_donations') ?? false))
                        ->form([
                            Forms\Components\Textarea::make('notes')
                                ->label('Alasan Penolakan')
                                ->required(),
                        ])
                        ->action(function (Donation $record, array $data): void {
                            $record->update([
                                'status' => DonationStatus::Rejected,
                                'verified_by' => auth()->id(),
                                'rejected_at' => now(),
                                'meta' => array_merge($record->meta ?? [], [
                                    'rejection_notes' => $data['notes'],
                                ]),
                            ]);
                        }),
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
            'index' => Pages\ListDonations::route('/'),
            'create' => Pages\CreateDonation::route('/create'),
            'edit' => Pages\EditDonation::route('/{record}/edit'),
        ];
    }
}
