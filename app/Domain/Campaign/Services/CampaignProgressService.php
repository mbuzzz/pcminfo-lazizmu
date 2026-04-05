<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Services;

use App\Domain\Campaign\Enums\CampaignProgressTypeEnum;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Campaign\Models\CampaignProgressSnapshot;
use App\Domain\Donation\Enums\DonationStatusEnum;
use App\Domain\Donation\Models\Donation;
use DomainException;

final class CampaignProgressService
{
    public function applyVerifiedDonation(Donation $donation): void
    {
        $this->syncDonationDelta($donation, 1);
    }

    public function revertVerifiedDonation(Donation $donation): void
    {
        $this->syncDonationDelta($donation, -1);
    }

    public function recalculate(Campaign $campaign): Campaign
    {
        $campaign = Campaign::query()->lockForUpdate()->findOrFail($campaign->getKey());

        $verifiedDonations = Donation::query()
            ->where('campaign_id', $campaign->getKey())
            ->where('status', DonationStatusEnum::Verified);

        $campaign->forceFill([
            'collected_amount' => (int) $verifiedDonations->sum('amount'),
            'collected_unit' => (int) $verifiedDonations->sum('quantity'),
            'verified_donor_count' => (int) $verifiedDonations->count(),
        ])->save();

        return $campaign->refresh();
    }

    /**
     * @return array<string, int|float>
     */
    public function summarize(Campaign $campaign): array
    {
        $currentValue = $campaign->progress_type === CampaignProgressTypeEnum::Amount
            ? (int) $campaign->collected_amount
            : (int) $campaign->collected_unit;
        $targetValue = $campaign->progress_type === CampaignProgressTypeEnum::Amount
            ? max(1, (int) $campaign->target_amount)
            : max(1, (int) $campaign->target_unit);

        return [
            'current_value' => $currentValue,
            'target_value' => $targetValue,
            'percentage' => round(($currentValue / $targetValue) * 100, 2),
        ];
    }

    private function syncDonationDelta(Donation $donation, int $direction): void
    {
        if ($donation->status !== DonationStatusEnum::Verified) {
            throw new DomainException('Only verified donation can affect campaign progress.');
        }

        $campaign = Campaign::query()->lockForUpdate()->findOrFail($donation->campaign_id);
        $beforeAmount = (int) $campaign->collected_amount;
        $beforeUnit = (int) $campaign->collected_unit;
        $deltaAmount = (int) $donation->amount * $direction;
        $deltaUnit = (int) $donation->quantity * $direction;

        $campaign->forceFill([
            'collected_amount' => max(0, $beforeAmount + $deltaAmount),
            'collected_unit' => max(0, $beforeUnit + $deltaUnit),
            'verified_donor_count' => max(0, (int) $campaign->verified_donor_count + $direction),
        ])->save();

        CampaignProgressSnapshot::query()->create([
            'campaign_id' => $campaign->getKey(),
            'source_type' => 'donation',
            'source_id' => $donation->getKey(),
            'delta_amount' => $deltaAmount,
            'delta_unit' => $deltaUnit,
            'before_amount' => $beforeAmount,
            'after_amount' => (int) $campaign->collected_amount,
            'before_unit' => $beforeUnit,
            'after_unit' => (int) $campaign->collected_unit,
            'meta' => [
                'status' => $donation->status->value,
                'transaction_code' => $donation->transaction_code,
            ],
        ]);
    }
}
