<?php

declare(strict_types=1);

namespace App\Domain\Setting\Models;

use App\Domain\Setting\Enums\SettingGroupEnum;
use App\Domain\Setting\Services\SettingService;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'group' => SettingGroupEnum::class,
            'value' => 'json',
            'is_public' => 'boolean',
        ];
    }

    public static function getValue(SettingGroupEnum|string $group, string $key, mixed $default = null): mixed
    {
        return app(SettingService::class)->get($group, $key, $default);
    }

    public static function setValue(
        SettingGroupEnum|string $group,
        string $key,
        mixed $value,
        string $type = 'json',
        bool $isPublic = false,
    ): self {
        return app(SettingService::class)->put($group, $key, $value, $type, $isPublic);
    }

    public static function getWithDefault(SettingGroupEnum|string $group, string $key, mixed $default = null): mixed
    {
        return static::getValue($group, $key, $default);
    }
}
