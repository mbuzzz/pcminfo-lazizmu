<?php

declare(strict_types=1);

namespace App\Domain\Donation\Actions;

use App\Domain\Donation\Data\VerifyDonationData;
use App\Domain\Donation\Models\Donation;
use App\Domain\Donation\Services\DonationVerificationService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class RejectDonationAction
{
    public function __construct(
        private readonly DonationVerificationService $verificationService,
    ) {}

    public function execute(Donation $donation, User $actor, VerifyDonationData $data): Donation
    {
        return DB::transaction(function () use ($donation, $actor, $data): Donation {
            $lockedDonation = Donation::query()->lockForUpdate()->findOrFail($donation->getKey());

            return $this->verificationService->reject($lockedDonation, $actor, $data);
        });
    }
}
