<?php

declare(strict_types=1);

namespace App\Domain\Setting\Queries;

use App\Domain\Setting\Enums\SettingGroupEnum;
use App\Domain\Setting\Models\Setting;
use Illuminate\Database\Eloquent\Collection;

final class SettingGroupQuery
{
    /**
     * @return Collection<int, Setting>
     */
    public function byGroup(SettingGroupEnum $group): Collection
    {
        return Setting::query()
            ->where('group', $group->value)
            ->orderBy('key')
            ->get();
    }
}
