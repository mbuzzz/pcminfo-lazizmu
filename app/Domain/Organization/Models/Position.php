<?php

declare(strict_types=1);

namespace App\Domain\Organization\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = 'positions';

    protected $fillable = [
        'name',
        'slug',
        'level',
        'sort_order',
    ];
}
