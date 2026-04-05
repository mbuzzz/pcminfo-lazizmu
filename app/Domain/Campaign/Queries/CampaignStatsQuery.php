<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Queries;

use App\Domain\Campaign\Models\Campaign;

final class CampaignStatsQuery
{
    /**
     * @return array<string, int>
     */
    public function summary(): array
    {
        return [
            'total' => Campaign::query()->count(),
            'active' => Campaign::query()->where('status', 'active')->count(),
            'completed' => Campaign::query()->where('status', 'completed')->count(),
            'total_target_value' => (int) Campaign::query()->sum('target_amount'),
        ];
    }
}
