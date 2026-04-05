<?php

declare(strict_types=1);

namespace App\Domain\Donation\Data;

use App\Application\DTOs\Data;

final class DonationData extends Data
{
    /**
     * @param  array<string, mixed>  $meta
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public int $campaignId,
        public string $payerName,
        public ?string $payerEmail,
        public ?string $donorPhone,
        public array $payload = [],
        public ?int $userId = null,
        public ?int $donorId = null,
        public ?int $amount = null,
        public ?int $quantity = null,
        public ?string $paymentMethod = 'manual_transfer',
        public ?string $paymentChannel = null,
        public ?string $message = null,
        public bool $isAnonymous = false,
        public array $meta = [],
        public ?string $idempotencyKey = null,
    ) {}
}
