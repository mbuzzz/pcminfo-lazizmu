<?php

declare(strict_types=1);

namespace App\Domain\Donation\Models;

use App\Domain\Campaign\Models\Campaign;
use App\Domain\Donation\Enums\DonationStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Donation extends Model
{
    protected $table = 'donations';

    protected $fillable = [
        'campaign_id',
        'donor_id',
        'user_id',
        'transaction_code',
        'idempotency_key',
        'payer_name',
        'payer_email',
        'payer_phone',
        'is_anonymous',
        'amount',
        'quantity',
        'unit_label',
        'message',
        'payment_method',
        'payment_channel',
        'status',
        'verified_by',
        'submitted_at',
        'verified_at',
        'rejected_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'quantity' => 'integer',
            'is_anonymous' => 'boolean',
            'status' => DonationStatusEnum::class,
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'rejected_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(DonationVerification::class);
    }

    public function latestVerification(): HasOne
    {
        return $this->hasOne(DonationVerification::class)->latestOfMany();
    }

    public function isVerified(): bool
    {
        return $this->status === DonationStatusEnum::Verified;
    }
}
