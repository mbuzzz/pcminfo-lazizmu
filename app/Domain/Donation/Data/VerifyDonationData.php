<?php

declare(strict_types=1);

namespace App\Domain\Donation\Data;

use App\Application\DTOs\Data;

final class VerifyDonationData extends Data
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public ?string $reason = null,
        public ?string $notes = null,
        public array $meta = [],
    ) {}
}
