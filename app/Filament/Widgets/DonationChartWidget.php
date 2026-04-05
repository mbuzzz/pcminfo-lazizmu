<?php

namespace App\Filament\Widgets;

use App\Models\Donation;
use App\Enums\DonationStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DonationChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;
    protected ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 'full';
    protected ?string $heading = 'Grafik Donasi Terverifikasi (6 Bulan Terakhir)';
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Loop for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);
            
            $labels[] = $date->translatedFormat('M Y');
            
            $sum = Donation::where('status', DonationStatus::Verified)
                ->whereYear('verified_at', $date->year)
                ->whereMonth('verified_at', $date->month)
                ->sum('amount');
                
            $data[] = $sum;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Donasi (Rp)',
                    'data' => $data,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)', // Emerald tint
                    'borderColor' => 'rgb(16, 185, 129)', // Emerald color
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
