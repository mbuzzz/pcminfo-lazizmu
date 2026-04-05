<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Enums;

enum CampaignProgressTypeEnum: string
{
    case Amount = 'amount';
    case Unit = 'unit';
}
