<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'published_at' => 'datetime',
        ];
    }
}
