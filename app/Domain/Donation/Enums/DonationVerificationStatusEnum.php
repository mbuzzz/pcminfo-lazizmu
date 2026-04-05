<?php

declare(strict_types=1);

namespace App\Domain\Donation\Enums;

enum DonationVerificationStatusEnum: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
