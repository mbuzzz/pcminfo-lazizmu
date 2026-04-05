<?php

declare(strict_types=1);

namespace App\Domain\Donation\Actions;

use App\Domain\Donation\Data\DonationData;
use App\Domain\Donation\Models\Donation;
use App\Domain\Donation\Services\DonationSubmissionService;
use Illuminate\Support\Facades\DB;

final class SubmitDonationAction
{
    public function __construct(
        private readonly DonationSubmissionService $donationSubmissionService,
    ) {}

    public function execute(DonationData $data): Donation
    {
        return DB::transaction(
            fn (): Donation => $this->donationSubmissionService->submit($data),
        );
    }
}
