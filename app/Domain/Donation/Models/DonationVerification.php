<?php

declare(strict_types=1);

namespace App\Domain\Donation\Models;

use App\Domain\Donation\Enums\DonationVerificationStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationVerification extends Model
{
    protected $table = 'donation_verifications';

    protected $fillable = [
        'donation_id',
        'verified_by',
        'status',
        'notes',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => DonationVerificationStatusEnum::class,
            'meta' => 'array',
        ];
    }

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
