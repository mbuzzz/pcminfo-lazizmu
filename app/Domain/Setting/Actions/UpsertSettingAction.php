<?php

declare(strict_types=1);

namespace App\Domain\Setting\Actions;

use App\Domain\Setting\Enums\SettingGroupEnum;
use App\Domain\Setting\Models\Setting;
use App\Domain\Setting\Services\SettingService;

final class UpsertSettingAction
{
    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    public function execute(SettingGroupEnum $group, string $key, mixed $value, string $type = 'json', bool $isPublic = false): Setting
    {
        return $this->settingService->put($group, $key, $value, $type, $isPublic);
    }
}
