<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * CampaignUpdate / Log Perkembangan Campaign
 *
 * Setiap update menyimpan snapshot progress saat ditulis agar bisa
 * digunakan untuk membuat grafik historis perkembangan campaign.
 * `media_files` JSON menyimpan path foto/video — atau gunakan Spatie
 * Media Library via collection 'update_media'.
 * `notify_donors` flag untuk trigger job notifikasi email ke donatur.
 */
class CampaignUpdate extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'campaign_updates';

    protected $fillable = [
        'campaign_id',
        'created_by',
        'title',
        'content',
        'media_files',
        'type',
        'amount_at_update',
        'unit_at_update',
        'donor_count_at_update',
        'notify_donors',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'media_files' => 'array',
            'notify_donors' => 'boolean',
            'published_at' => 'datetime',
            'amount_at_update' => 'integer',
            'unit_at_update' => 'integer',
            'donor_count_at_update' => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Media Library
    // -------------------------------------------------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('update_media')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'video/mp4']);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('published_at', 'desc');
    }
}
