<?php

declare(strict_types=1);

namespace App\Domain\Donation\Services;

use App\Domain\Donation\Data\VerifyDonationData;
use App\Domain\Donation\Enums\DonationStatusEnum;
use App\Domain\Donation\Enums\DonationVerificationStatusEnum;
use App\Domain\Donation\Models\Donation;
use App\Domain\Donation\Models\DonationVerification;
use App\Models\User;
use DomainException;

final class DonationVerificationService
{
    public function approve(Donation $donation, User $actor, VerifyDonationData $data): Donation
    {
        if ($donation->status !== DonationStatusEnum::Pending) {
            throw new DomainException('Only pending donation can be approved.');
        }

        $donation->forceFill([
            'status' => DonationStatusEnum::Verified,
            'verified_by' => $actor->getKey(),
            'verified_at' => now(),
            'rejected_at' => null,
        ])->save();

        DonationVerification::query()->create([
            'donation_id' => $donation->getKey(),
            'verified_by' => $actor->getKey(),
            'status' => DonationVerificationStatusEnum::Approved,
            'notes' => $data->notes,
            'meta' => $data->meta,
        ]);

        return $donation->refresh();
    }

    public function reject(Donation $donation, User $actor, VerifyDonationData $data): Donation
    {
        if ($donation->status !== DonationStatusEnum::Pending) {
            throw new DomainException('Only pending donation can be rejected.');
        }

        if ($data->reason === null || trim($data->reason) === '') {
            throw new DomainException('Reject reason is required.');
        }

        $donation->forceFill([
            'status' => DonationStatusEnum::Rejected,
            'verified_by' => $actor->getKey(),
            'rejected_at' => now(),
        ])->save();

        DonationVerification::query()->create([
            'donation_id' => $donation->getKey(),
            'verified_by' => $actor->getKey(),
            'status' => DonationVerificationStatusEnum::Rejected,
            'notes' => $data->notes,
            'meta' => array_merge($data->meta, [
                'reason' => $data->reason,
            ]),
        ]);

        return $donation->refresh();
    }
}
