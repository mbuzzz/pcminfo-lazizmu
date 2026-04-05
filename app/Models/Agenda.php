<?php

namespace App\Models;

use App\Enums\AgendaStatus;
use App\Enums\AgendaType;
use App\Domain\Setting\Services\SiteSettingService;
use App\Services\Media\MediaUploadService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Str;

/**
 * Agenda / Kegiatan Model
 *
 * `recurrence_rule` mengikuti format iCal RRULE (FREQ=WEEKLY;BYDAY=SU).
 * `registered_count` denormalized untuk efisiensi tampilan daftar kegiatan
 * tanpa COUNT() join — dikelola via AgendaRegistration observer.
 * Scope `upcoming` + `ongoing` berguna untuk widget dashboard.
 */
class Agenda extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'institution_id',
        'created_by',
        'title',
        'slug',
        'description',
        'featured_image',
        'type',
        'status',
        'start_at',
        'end_at',
        'location_name',
        'location_address',
        'maps_url',
        'is_online',
        'meeting_url',
        'requires_registration',
        'max_participants',
        'registered_count',
        'is_recurring',
        'recurrence_rule',
        'contact_name',
        'contact_phone',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'type' => AgendaType::class,
            'status' => AgendaStatus::class,
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_online' => 'boolean',
            'requires_registration' => 'boolean',
            'is_recurring' => 'boolean',
            'is_featured' => 'boolean',
            'max_participants' => 'integer',
            'registered_count' => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Media Library
    // -------------------------------------------------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')
            ->singleFile();

        $this->addMediaCollection('documents'); // Materi/rundown event
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(AgendaRegistration::class);
    }

    public function confirmedRegistrations(): HasMany
    {
        return $this->hasMany(AgendaRegistration::class)->where('status', 'confirmed');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', AgendaStatus::Published);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query
            ->where('status', AgendaStatus::Published)
            ->where('start_at', '>', now())
            ->orderBy('start_at');
    }

    public function scopeOngoing(Builder $query): Builder
    {
        return $query
            ->where('status', AgendaStatus::Published)
            ->where('start_at', '<=', now())
            ->where(function (Builder $q) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', now());
            });
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('end_at', '<', now())
            ->orWhere(function (Builder $q) {
                $q->whereNull('end_at')->where('start_at', '<', now());
            });
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeRequiresRegistration(Builder $query): Builder
    {
        return $query->where('requires_registration', true);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isUpcoming(): bool
    {
        return $this->start_at->isFuture();
    }

    public function isOngoing(): bool
    {
        return $this->start_at->isPast()
            && ($this->end_at === null || $this->end_at->isFuture());
    }

    public function isPast(): bool
    {
        return $this->end_at?->isPast()
            ?? $this->start_at->isPast();
    }

    /** Sisa kuota (null jika tidak ada batas) */
    public function getRemainingSlots(): ?int
    {
        if ($this->max_participants === null) {
            return null;
        }

        return max(0, $this->max_participants - $this->registered_count);
    }

    public function isFull(): bool
    {
        if ($this->max_participants === null) {
            return false;
        }

        return $this->registered_count >= $this->max_participants;
    }

    public function getSeoTitleAttribute(): string
    {
        return $this->title
            ?: (string) app(SiteSettingService::class)->getWithDefault('default_meta_title');
    }

    public function getSeoDescriptionAttribute(): ?string
    {
        return Str::limit(
            strip_tags((string) ($this->description ?: app(SiteSettingService::class)->getWithDefault('default_meta_description'))),
            160,
            '',
        );
    }

    public function getSeoImageUrlAttribute(): ?string
    {
        return app(MediaUploadService::class)->url(
            app(SiteSettingService::class)->getWithDefault('default_og_image')
        );
    }

    public function getSeoMetaAttribute(): array
    {
        return [
            'title' => $this->seo_title,
            'description' => $this->seo_description,
            'image' => $this->seo_image_url,
        ];
    }
}
