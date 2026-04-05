<?php

namespace App\Models;

use App\Enums\DistributionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Distribution / Penyaluran Model
 *
 * Two-level authorization:
 * 1. `approved_by` — ketua Lazismu menyetujui rencana penyaluran
 * 2. `distributed_by` — amil mencatat eksekusi lapangan
 *
 * `evidence_files` JSON menyimpan array path foto/dokumen lapangan.
 * `meta` JSON untuk data penerima detail (NIK, alamat, kondisi).
 * `recipient_type` mengikuti 8 asnaf + general + institution.
 */
class Distribution extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'campaign_id',
        'institution_id',
        'distribution_code',
        'title',
        'description',
        'recipient_type',
        'recipient_name',
        'recipient_count',
        'distributed_amount',
        'distributed_unit',
        'unit_label',
        'distribution_type',
        'status',
        'distribution_date',
        'location',
        'evidence_files',
        'notes',
        'meta',
        'created_by',
        'approved_by',
        'approved_at',
        'distributed_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => DistributionStatus::class,
            'distribution_date' => 'date',
            'approved_at' => 'datetime',
            'evidence_files' => 'array',
            'meta' => 'array',
            'distributed_amount' => 'integer',
            'distributed_unit' => 'integer',
            'recipient_count' => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Media Library — Bukti Penyaluran
    // -------------------------------------------------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('evidence')
            ->acceptsMimeTypes([
                'image/jpeg', 'image/png', 'image/webp',
                'application/pdf',
            ]);

        $this->addMediaCollection('berita_acara')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png']);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function distributedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', DistributionStatus::Draft);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', DistributionStatus::Approved);
    }

    public function scopeDistributed(Builder $query): Builder
    {
        return $query->where('status', DistributionStatus::Distributed);
    }

    public function scopeReported(Builder $query): Builder
    {
        return $query->where('status', DistributionStatus::Reported);
    }

    /** Filter berdasarkan jenis asnaf */
    public function scopeForAsnaf(Builder $query, string $recipientType): Builder
    {
        return $query->where('recipient_type', $recipientType);
    }

    public function scopeForCampaign(Builder $query, int $campaignId): Builder
    {
        return $query->where('campaign_id', $campaignId);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('distribution_date', now()->month)
            ->whereYear('distribution_date', now()->year);
    }

    // -------------------------------------------------------------------------
    // Computed Attributes
    // -------------------------------------------------------------------------

    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn (): string => 'Rp '.number_format($this->distributed_amount, 0, ',', '.')
        );
    }

    protected function isFullyReported(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->status === DistributionStatus::Reported
                                && $this->getMedia('evidence')->isNotEmpty()
        );
    }

    // -------------------------------------------------------------------------
    // Helpers — Meta Penerima
    // -------------------------------------------------------------------------

    /**
     * Ambil data penerima dari JSON meta.
     * Contoh: $distribution->getRecipients() → array of recipient data
     */
    public function getRecipients(): array
    {
        return data_get($this->meta, 'recipients', []);
    }

    public function getMeta(string $key, mixed $default = null): mixed
    {
        return data_get($this->meta, $key, $default);
    }

    public function canBeApproved(): bool
    {
        return $this->status === DistributionStatus::Draft;
    }

    public function canBeDistributed(): bool
    {
        return $this->status === DistributionStatus::Approved;
    }
}
