<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

/**
 * CampaignType — Jenis program penggalangan Lazismu.
 * Dipakai di Filament SelectFilter, badge column, dan form Select.
 */
enum CampaignType: string implements HasColor, HasIcon, HasLabel
{
    case Donation = 'donation';
    case ZakatFitrah = 'zakat_fitrah';
    case ZakatMaal = 'zakat_maal';
    case Infaq = 'infaq';
    case Sedekah = 'sedekah';
    case Wakaf = 'wakaf';
    case Qurban = 'qurban';
    case Emergency = 'emergency';
    case Social = 'social';
    case Scholarship = 'scholarship';

    public function getLabel(): string
    {
        return match ($this) {
            self::Donation => 'Donasi Umum',
            self::ZakatFitrah => 'Zakat Fitrah',
            self::ZakatMaal => 'Zakat Maal',
            self::Infaq => 'Infaq',
            self::Sedekah => 'Sedekah',
            self::Wakaf => 'Wakaf',
            self::Qurban => 'Qurban',
            self::Emergency => 'Darurat / Bencana',
            self::Social => 'Sosial',
            self::Scholarship => 'Beasiswa',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Donation => 'gray',
            self::ZakatFitrah => 'success',
            self::ZakatMaal => 'success',
            self::Infaq => 'info',
            self::Sedekah => 'info',
            self::Wakaf => 'warning',
            self::Qurban => 'warning',
            self::Emergency => 'danger',
            self::Social => 'gray',
            self::Scholarship => 'primary',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Donation => 'heroicon-o-heart',
            self::ZakatFitrah => 'heroicon-o-moon',
            self::ZakatMaal => 'heroicon-o-currency-dollar',
            self::Infaq => 'heroicon-o-heart',
            self::Sedekah => 'heroicon-o-gift',
            self::Wakaf => 'heroicon-o-building-library',
            self::Qurban => 'heroicon-o-star',
            self::Emergency => 'heroicon-o-exclamation-triangle',
            self::Social => 'heroicon-o-users',
            self::Scholarship => 'heroicon-o-academic-cap',
        };
    }
}
