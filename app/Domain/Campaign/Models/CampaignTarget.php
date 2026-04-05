<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Models;

use App\Domain\Campaign\Enums\CampaignProgressTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignTarget extends Model
{
    protected $table = 'campaign_targets';

    protected $fillable = [
        'campaign_id',
        'progress_type',
        'target_value',
        'current_value',
    ];

    protected function casts(): array
    {
        return [
            'progress_type' => CampaignProgressTypeEnum::class,
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
