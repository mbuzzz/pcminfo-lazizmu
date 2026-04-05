<?php

declare(strict_types=1);

namespace App\Domain\Donation\Enums;

enum DonationStatusEnum: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';
}
