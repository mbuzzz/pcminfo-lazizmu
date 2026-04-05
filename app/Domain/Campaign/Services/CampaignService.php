<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Services;

use App\Domain\Campaign\Data\CampaignData;
use App\Domain\Campaign\Enums\CampaignTypeEnum;
use App\Domain\Campaign\Models\Campaign;
use App\Domain\Campaign\Models\CampaignUnitOption;
use InvalidArgumentException;

final class CampaignService
{
    public function __construct(
        private readonly CampaignConfigService $configService,
        private readonly CampaignFormService $campaignFormService,
    ) {}

    public function create(CampaignData $data): Campaign
    {
        $this->configService->assertValidConfig($data->config, $data->type, $data->progressType);
        $this->campaignFormService->validate($data->config);

        if ($data->type === CampaignTypeEnum::Qurban && $data->unitOptions === []) {
            throw new InvalidArgumentException('Qurban campaign must define at least one unit option.');
        }

        $campaign = Campaign::query()->create([
            'institution_id' => $data->institutionId,
            'created_by' => $data->createdBy,
            'title' => $data->title,
            'slug' => $data->slug,
            'short_description' => $data->shortDescription,
            'description' => $data->description,
            'type' => $data->type,
            'status' => $data->status,
            'progress_type' => $data->progressType,
            'target_amount' => $data->targetAmount,
            'target_unit' => $data->targetUnit,
            'unit_label' => $data->unitLabel,
            'start_date' => $data->startDate,
            'end_date' => $data->endDate,
            'config' => $data->config,
            'payment_config' => $data->paymentConfig,
            'beneficiary_name' => $data->beneficiaryName,
            'beneficiary_description' => $data->beneficiaryDescription,
            'published_at' => $data->status->value === 'active' ? now() : null,
        ]);

        foreach ($data->unitOptions as $index => $option) {
            CampaignUnitOption::query()->create([
                'campaign_id' => $campaign->getKey(),
                'code' => (string) ($option['code'] ?? ''),
                'label' => (string) ($option['label'] ?? ''),
                'unit_value' => (int) ($option['unit_value'] ?? 1),
                'amount' => (int) ($option['amount'] ?? 0),
                'sort_order' => (int) ($option['sort_order'] ?? $index),
                'is_active' => (bool) ($option['is_active'] ?? true),
                'meta' => is_array($option['meta'] ?? null) ? $option['meta'] : [],
            ]);
        }

        return $campaign->load('unitOptions');
    }
}
