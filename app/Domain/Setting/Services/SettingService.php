<?php

declare(strict_types=1);

namespace App\Domain\Setting\Services;

use App\Domain\Setting\Enums\SettingGroupEnum;
use App\Domain\Setting\Models\Setting;
use Illuminate\Support\Facades\Cache;

final class SettingService
{
    /**
     * @return array<string, mixed>
     */
    public function group(SettingGroupEnum|string $group): array
    {
        $group = $this->normalizeGroup($group);

        return Cache::remember(
            $this->cacheKey($group),
            now()->addDay(),
            static fn (): array => Setting::query()
                ->where('group', $group->value)
                ->get(['key', 'value'])
                ->mapWithKeys(static fn (Setting $setting): array => [
                    $setting->key => $setting->value,
                ])
                ->all(),
        );
    }

    public function put(
        SettingGroupEnum|string $group,
        string $key,
        mixed $value,
        string $type = 'json',
        bool $isPublic = false,
    ): Setting
    {
        $group = $this->normalizeGroup($group);

        $setting = Setting::query()->updateOrCreate(
            ['group' => $group->value, 'key' => $key],
            ['value' => $value, 'type' => $type, 'is_public' => $isPublic],
        );

        $this->forgetGroupCache($group);

        return $setting;
    }

    public function get(SettingGroupEnum|string $group, string $key, mixed $default = null): mixed
    {
        return $this->group($group)[$key] ?? $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function putMany(SettingGroupEnum|string $group, array $data, bool $isPublic = false): void
    {
        $group = $this->normalizeGroup($group);

        foreach ($data as $key => $value) {
            Setting::query()->updateOrCreate(
                ['group' => $group->value, 'key' => $key],
                ['value' => $value, 'type' => 'json', 'is_public' => $isPublic],
            );
        }

        $this->forgetGroupCache($group);
    }

    public function forgetGroupCache(SettingGroupEnum|string $group): void
    {
        $group = $this->normalizeGroup($group);

        Cache::forget($this->cacheKey($group));
    }

    private function cacheKey(SettingGroupEnum $group): string
    {
        return "settings.group.{$group->value}";
    }

    private function normalizeGroup(SettingGroupEnum|string $group): SettingGroupEnum
    {
        if ($group instanceof SettingGroupEnum) {
            return $group;
        }

        return SettingGroupEnum::from($group);
    }
}
