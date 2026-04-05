<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Services;

use App\Domain\Campaign\Models\Campaign;
use DomainException;

final class CampaignEligibilityService
{
    public function assertAcceptingDonation(Campaign $campaign): void
    {
        if (! $campaign->isOpenForDonation()) {
            throw new DomainException('Campaign is not accepting donations.');
        }
    }
}
