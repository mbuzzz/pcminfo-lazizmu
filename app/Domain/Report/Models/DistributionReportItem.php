<?php

declare(strict_types=1);

namespace App\Domain\Report\Models;

use Illuminate\Database\Eloquent\Model;

class DistributionReportItem extends Model
{
    protected $table = 'distribution_report_items';

    protected $fillable = [
        'distribution_report_id',
        'title',
        'value',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}
