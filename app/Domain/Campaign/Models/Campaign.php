<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Models;

use App\Domain\Campaign\Enums\CampaignProgressTypeEnum;
use App\Domain\Campaign\Enums\CampaignStatusEnum;
use App\Domain\Campaign\Enums\CampaignTypeEnum;
use App\Domain\Donation\Enums\DonationStatusEnum;
use App\Domain\Donation\Models\Donation;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $table = 'campaigns';

    protected $fillable = [
        'institution_id',
        'created_by',
        'short_description',
        'description',
        'title',
        'slug',
        'type',
        'status',
        'progress_type',
        'target_amount',
        'target_unit',
        'unit_label',
        'collected_amount',
        'collected_unit',
        'verified_donor_count',
        'start_date',
        'end_date',
        'published_at',
        'closed_at',
        'config',
        'payment_config',
        'beneficiary_name',
        'beneficiary_description',
        'allow_anonymous',
        'show_donor_list',
    ];

    protected function casts(): array
    {
        return [
            'type' => CampaignTypeEnum::class,
            'status' => CampaignStatusEnum::class,
            'progress_type' => CampaignProgressTypeEnum::class,
            'target_amount' => 'integer',
            'target_unit' => 'integer',
            'collected_amount' => 'integer',
            'collected_unit' => 'integer',
            'verified_donor_count' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
            'config' => 'array',
            'payment_config' => 'array',
            'allow_anonymous' => 'boolean',
            'show_donor_list' => 'boolean',
        ];
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function verifiedDonations(): HasMany
    {
        return $this->donations()->where('status', DonationStatusEnum::Verified);
    }

    public function unitOptions(): HasMany
    {
        return $this->hasMany(CampaignUnitOption::class)->orderBy('sort_order');
    }

    public function progressSnapshots(): HasMany
    {
        return $this->hasMany(CampaignProgressSnapshot::class)->latest();
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CampaignStatusEnum::Active);
    }

    public function scopePubliclyAvailable(Builder $query): Builder
    {
        return $query
            ->where('status', CampaignStatusEnum::Active)
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('start_date')
                    ->orWhere('start_date', '<=', now()->toDateString());
            })
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            });
    }

    public function isOpenForDonation(): bool
    {
        if ($this->status !== CampaignStatusEnum::Active) {
            return false;
        }

        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }

        if ($this->end_date && $this->end_date->toDateString() < now()->toDateString()) {
            return false;
        }

        return $this->closed_at === null;
    }
}
