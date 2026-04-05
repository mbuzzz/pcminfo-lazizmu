<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Enums;

enum CampaignStatusEnum: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Paused = 'paused';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
