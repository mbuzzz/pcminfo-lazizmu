<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Post;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Distribution;
use App\Models\Institution;
use App\Models\Leader;
use App\Models\Agenda;
use App\Enums\CampaignStatus;
use App\Enums\DonationStatus;

class DashboardStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Donasi', 'Rp ' . number_format(Donation::where('status', DonationStatus::Verified)->sum('amount'), 0, ',', '.'))
                ->description('Dana terverifikasi')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
            Stat::make('Total Penyaluran', 'Rp ' . number_format(Distribution::sum('distributed_amount'), 0, ',', '.'))
                ->description('Telah tersalurkan')
                ->descriptionIcon('heroicon-m-hand-raised')
                ->color('info'),
            Stat::make('Program Aktif', Campaign::where('status', CampaignStatus::Active)->count())
                ->description('Campaign berjalan')
                ->descriptionIcon('heroicon-m-heart')
                ->color('warning'),
            Stat::make('Amal Usaha', Institution::count())
                ->description('Total instansi terdaftar')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('primary'),
            Stat::make('Struktur Pimpinan', Leader::where('status', 'active')->count())
                ->description('Profil Pimpinan & Ortom')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
            Stat::make('Artikel Berita', Post::count())
                ->description('Publikasi tercatat')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('gray'),
            Stat::make('Total Agenda', Agenda::count())
                ->description('Rekam jadwal & acara')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('gray'),
        ];
    }
}
