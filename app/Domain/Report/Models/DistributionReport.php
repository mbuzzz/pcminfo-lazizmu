<?php

declare(strict_types=1);

namespace App\Domain\Report\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionReport extends Model
{
    protected $table = 'distribution_reports';

    protected $fillable = [
        'campaign_id',
        'title',
        'content',
        'status',
        'published_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'meta' => 'array',
        ];
    }
}
