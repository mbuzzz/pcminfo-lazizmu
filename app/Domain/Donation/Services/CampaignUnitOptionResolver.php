<?php

declare(strict_types=1);

namespace App\Domain\Donation\Services;

use App\Domain\Campaign\Models\Campaign;
use App\Domain\Campaign\Models\CampaignUnitOption;
use DomainException;

final class CampaignUnitOptionResolver
{
    public function resolve(Campaign $campaign, string $optionCode): CampaignUnitOption
    {
        $option = $campaign->unitOptions()
            ->where('code', $optionCode)
            ->where('is_active', true)
            ->first();

        if (! $option) {
            throw new DomainException('Selected campaign unit option is invalid.');
        }

        return $option;
    }
}
