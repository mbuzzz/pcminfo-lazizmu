<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Services;

use App\Domain\Campaign\Enums\CampaignProgressTypeEnum;
use App\Domain\Campaign\Enums\CampaignTypeEnum;
use App\Domain\Campaign\Models\Campaign;
use InvalidArgumentException;

final class CampaignConfigService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getFormSchema(Campaign|array $campaign): array
    {
        $config = $campaign instanceof Campaign ? ($campaign->config ?? []) : $campaign;

        return data_get($config, 'form.fields', []);
    }

    /**
     * @return array<string, mixed>
     */
    public function getBehavior(Campaign|array $campaign): array
    {
        $config = $campaign instanceof Campaign ? ($campaign->config ?? []) : $campaign;

        return data_get($config, 'behavior', []);
    }

    /**
     * @return array<string, mixed>
     */
    public function getProgressConfig(Campaign|array $campaign): array
    {
        $config = $campaign instanceof Campaign ? ($campaign->config ?? []) : $campaign;

        return data_get($config, 'progress', []);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public function assertValidConfig(array $config, CampaignTypeEnum $campaignType, CampaignProgressTypeEnum $progressType): void
    {
        $version = data_get($config, 'version');
        $fields = $this->getFormSchema($config);
        $progress = $this->getProgressConfig($config);

        if (! is_int($version) || $version < 1) {
            throw new InvalidArgumentException('Campaign config version must be a positive integer.');
        }

        if (data_get($config, 'type') !== $campaignType->value) {
            throw new InvalidArgumentException('Campaign config type must match campaign type.');
        }

        if (! is_array($fields) || $fields === []) {
            throw new InvalidArgumentException('Campaign config form.fields must contain at least one field.');
        }

        if (data_get($progress, 'type') !== $progressType->value) {
            throw new InvalidArgumentException('Campaign progress.type must match campaign progress type.');
        }

        if ($progressType === CampaignProgressTypeEnum::Amount && ! is_int(data_get($progress, 'target_amount'))) {
            throw new InvalidArgumentException('Amount-based campaign must define progress.target_amount.');
        }

        if ($progressType === CampaignProgressTypeEnum::Unit) {
            if (! is_int(data_get($progress, 'target_unit'))) {
                throw new InvalidArgumentException('Unit-based campaign must define progress.target_unit.');
            }

            if (! is_string(data_get($progress, 'unit_label'))) {
                throw new InvalidArgumentException('Unit-based campaign must define progress.unit_label.');
            }
        }
    }
}
