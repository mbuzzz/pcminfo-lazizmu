<?php

declare(strict_types=1);

namespace App\Domain\Setting\Enums;

enum SettingGroupEnum: string
{
    case App = 'app';
    case Organization = 'organization';
    case Lazismu = 'lazismu';
    case Payment = 'payment';
    case WhatsApp = 'whatsapp';
    case Storage = 'storage';
}
