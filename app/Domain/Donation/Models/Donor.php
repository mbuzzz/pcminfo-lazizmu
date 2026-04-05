<?php

declare(strict_types=1);

namespace App\Domain\Donation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    protected $table = 'donors';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}
