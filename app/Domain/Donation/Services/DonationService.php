<?php

declare(strict_types=1);

namespace App\Domain\Donation\Services;

use App\Domain\Donation\Data\DonationData;
use App\Domain\Donation\Models\Donation;

final class DonationService
{
    public function __construct(
        private readonly DonationSubmissionService $submissionService,
    ) {}

    public function submit(DonationData $data): Donation
    {
        return $this->submissionService->submit($data);
    }
}
