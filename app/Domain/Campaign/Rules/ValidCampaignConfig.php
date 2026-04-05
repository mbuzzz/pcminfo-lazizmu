<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Rules;

use App\Domain\Campaign\Enums\CampaignProgressTypeEnum;
use App\Domain\Campaign\Enums\CampaignTypeEnum;
use App\Domain\Campaign\Services\CampaignConfigService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Throwable;

final class ValidCampaignConfig implements ValidationRule
{
    public function __construct(
        private readonly CampaignConfigService $configService,
        private readonly CampaignTypeEnum $campaignType,
        private readonly CampaignProgressTypeEnum $progressType,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $this->configService->assertValidConfig((array) $value, $this->campaignType, $this->progressType);
        } catch (Throwable $throwable) {
            $fail($throwable->getMessage());
        }
    }
}
