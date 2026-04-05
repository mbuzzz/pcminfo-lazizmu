<?php

declare(strict_types=1);

namespace App\Domain\Donation\Actions;

use App\Domain\Campaign\Services\CampaignProgressService;
use App\Domain\Donation\Data\VerifyDonationData;
use App\Domain\Donation\Models\Donation;
use App\Domain\Donation\Services\DonationVerificationService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class ApproveDonationAction
{
    public function __construct(
        private readonly DonationVerificationService $verificationService,
        private readonly CampaignProgressService $campaignProgressService,
    ) {}

    public function execute(Donation $donation, User $actor, VerifyDonationData $data): Donation
    {
        return DB::transaction(function () use ($donation, $actor, $data): Donation {
            $lockedDonation = Donation::query()->lockForUpdate()->findOrFail($donation->getKey());
            $verifiedDonation = $this->verificationService->approve($lockedDonation, $actor, $data);

            $this->campaignProgressService->applyVerifiedDonation($verifiedDonation);

            return $verifiedDonation->refresh();
        });
    }
}
