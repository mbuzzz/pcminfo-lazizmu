<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DonationPaymentStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Expired = 'expired';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Pembayaran',
            self::Paid => 'Terbayar',
            self::Failed => 'Gagal',
            self::Refunded => 'Dikembalikan',
            self::Expired => 'Kadaluarsa',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Failed => 'danger',
            self::Refunded => 'info',
            self::Expired => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Paid => 'heroicon-o-check-badge',
            self::Failed => 'heroicon-o-x-circle',
            self::Refunded => 'heroicon-o-arrow-uturn-left',
            self::Expired => 'heroicon-o-calendar-x-mark',
        };
    }
}
