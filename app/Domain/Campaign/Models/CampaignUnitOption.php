<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignUnitOption extends Model
{
    protected $table = 'campaign_unit_options';

    protected $fillable = [
        'campaign_id',
        'code',
        'label',
        'unit_value',
        'amount',
        'sort_order',
        'is_active',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'unit_value' => 'integer',
            'amount' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
