<?php

declare(strict_types=1);

namespace App\Domain\Donation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentChannel extends Model
{
    protected $table = 'payment_channels';

    protected $fillable = [
        'name',
        'code',
        'type',
        'is_active',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'meta' => 'array',
        ];
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}
