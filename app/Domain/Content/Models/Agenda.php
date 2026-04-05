<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agendas';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'location',
        'start_at',
        'end_at',
        'status',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'meta' => 'array',
        ];
    }
}
