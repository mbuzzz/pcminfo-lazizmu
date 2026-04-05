<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Data;

use App\Application\DTOs\Data;
use App\Domain\Campaign\Enums\CampaignProgressTypeEnum;
use App\Domain\Campaign\Enums\CampaignStatusEnum;
use App\Domain\Campaign\Enums\CampaignTypeEnum;

final class CampaignData extends Data
{
    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $paymentConfig
     * @param  array<int, array<string, mixed>>  $unitOptions
     */
    public function __construct(
        public CampaignTypeEnum $type,
        public string $title,
        public string $slug,
        public CampaignStatusEnum $status,
        public CampaignProgressTypeEnum $progressType,
        public ?int $targetAmount = null,
        public ?int $targetUnit = null,
        public ?string $unitLabel = null,
        public ?int $institutionId = null,
        public ?int $createdBy = null,
        public ?string $shortDescription = null,
        public ?string $description = null,
        public ?string $beneficiaryName = null,
        public ?string $beneficiaryDescription = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public array $config = [],
        public array $paymentConfig = [],
        public array $unitOptions = [],
    ) {}
}
