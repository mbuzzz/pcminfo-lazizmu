<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignForm extends Model
{
    protected $table = 'campaign_forms';

    protected $fillable = [
        'campaign_id',
        'schema',
        'schema_version',
    ];

    protected function casts(): array
    {
        return [
            'schema' => 'array',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
