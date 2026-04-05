<?php

declare(strict_types=1);

namespace App\Domain\Organization\Models;

use Illuminate\Database\Eloquent\Model;

class LeadershipPeriod extends Model
{
    protected $table = 'leadership_periods';

    protected $fillable = [
        'name',
        'start_year',
        'end_year',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
