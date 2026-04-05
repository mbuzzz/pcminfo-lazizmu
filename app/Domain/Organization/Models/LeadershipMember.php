<?php

declare(strict_types=1);

namespace App\Domain\Organization\Models;

use Illuminate\Database\Eloquent\Model;

class LeadershipMember extends Model
{
    protected $table = 'leadership_members';

    protected $fillable = [
        'leadership_period_id',
        'position_id',
        'name',
        'bio',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
