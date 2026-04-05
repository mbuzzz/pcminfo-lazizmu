<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DonationPaymentMethod: string implements HasColor, HasIcon, HasLabel
{
    case ManualTransfer = 'manual_transfer';
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case Qris = 'qris';
    case VirtualAccount = 'virtual_account';
    case EWallet = 'e_wallet';
    case CreditCard = 'credit_card';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::ManualTransfer => 'Transfer Manual',
            self::Cash => 'Tunai',
            self::BankTransfer => 'Transfer Bank',
            self::Qris => 'QRIS',
            self::VirtualAccount => 'Virtual Account',
            self::EWallet => 'E-Wallet (OVO/Dana/GoPay)',
            self::CreditCard => 'Kartu Kredit',
            self::Other => 'Lainnya',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ManualTransfer => 'info',
            self::Cash => 'success',
            self::BankTransfer => 'info',
            self::Qris => 'primary',
            self::VirtualAccount => 'warning',
            self::EWallet => 'success',
            self::CreditCard => 'gray',
            self::Other => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ManualTransfer => 'heroicon-o-building-library',
            self::Cash => 'heroicon-o-banknotes',
            self::BankTransfer => 'heroicon-o-building-library',
            self::Qris => 'heroicon-o-qr-code',
            self::VirtualAccount => 'heroicon-o-credit-card',
            self::EWallet => 'heroicon-o-device-phone-mobile',
            self::CreditCard => 'heroicon-o-credit-card',
            self::Other => 'heroicon-o-ellipsis-horizontal-circle',
        };
    }
}
