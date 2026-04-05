<?php

declare(strict_types=1);

namespace App\Domain\Organization\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessUnit extends Model
{
    protected $table = 'business_units';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }
}
