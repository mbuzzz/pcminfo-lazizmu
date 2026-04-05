<?php

namespace App\Filament\Widgets;

use App\Models\Donation;
use App\Enums\DonationStatus;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Domain\Donation\Services\DonationVerificationService;

class LatestPendingDonationsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Donation::query()
                    ->where('status', DonationStatus::Pending)
                    ->latest()
                    ->limit(5)
            )
            ->heading('Donasi Menunggu Verifikasi')
            ->description('Segera proses verifikasi agar penyaluran bisa dilaksanakan.')
            ->columns([
                Tables\Columns\TextColumn::make('donor_name')
                    ->label('Nama Donatur')
                    ->searchable()
                    ->description(fn (Donation $record): string => $record->donor_phone ?? '-'),
                Tables\Columns\TextColumn::make('campaign.title')
                    ->label('Tujuan Program')
                    ->limit(40),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                \Filament\Actions\Action::make('verify')
                    ->label('Verifikasi')
                    ->color('success')
                    ->icon('heroicon-m-check-circle')
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
            ]);
    }
}
