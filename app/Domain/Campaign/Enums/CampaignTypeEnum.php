<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Enums;

enum CampaignTypeEnum: string
{
    case Donation = 'donation';
    case Qurban = 'qurban';
    case Zakat = 'zakat';
    case Wakaf = 'wakaf';
    case Program = 'program';
}
