<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignProgressSnapshot extends Model
{
    protected $table = 'campaign_progress_snapshots';

    protected $fillable = [
        'campaign_id',
        'source_type',
        'source_id',
        'delta_amount',
        'delta_unit',
        'before_amount',
        'after_amount',
        'before_unit',
        'after_unit',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'delta_amount' => 'integer',
            'delta_unit' => 'integer',
            'before_amount' => 'integer',
            'after_amount' => 'integer',
            'before_unit' => 'integer',
            'after_unit' => 'integer',
            'meta' => 'array',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
