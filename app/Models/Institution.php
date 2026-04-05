<?php

namespace App\Models;

use App\Enums\InstitutionType;
use App\Models\Concerns\HasMediaUrl;
use App\Services\Media\MediaUploadService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Institution / Amal Usaha Model
 *
 * File media disimpan sebagai path storage sederhana agar tetap ringan
 * dan mudah dipindah ke S3 tanpa mengubah kontrak model.
 * `meta` JSON dinamis per type — diakses via helper getMetaValue().
 */
class Institution extends Model
{
    use HasMediaUrl;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'acronym',
        'tagline',
        'description',
        'type',
        'status',
        'address',
        'village',
        'district',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'logo',
        'cover_image',
        'founded_year',
        'accreditation',
        'meta',
        'is_featured',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'type' => InstitutionType::class,
            'meta' => 'array',
            'is_featured' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
            'order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::forceDeleted(function (Institution $institution): void {
            $media = app(MediaUploadService::class);
            $media->delete($institution->logo);
            $media->delete($institution->cover_image);
        });
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function leaders(): HasMany
    {
        return $this->hasMany(Leader::class);
    }

    public function activeLeaders(): HasMany
    {
        return $this->hasMany(Leader::class)->where('status', 'active')->orderBy('order');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function agendas(): HasMany
    {
        return $this->hasMany(Agenda::class);
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(Distribution::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfType(Builder $query, InstitutionType|string $type): Builder
    {
        return $query->where('type', $type instanceof InstitutionType ? $type->value : $type);
    }

    public function scopeInCity(Builder $query, string $city): Builder
    {
        return $query->where('city', $city);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Ambil nilai dari JSON meta, dengan fallback default */
    public function getMetaValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->meta, $key, $default);
    }

    public function hasCoordinates(): bool
    {
        return ! is_null($this->latitude) && ! is_null($this->longitude);
    }

    public function getGoogleMapsUrl(): ?string
    {
        if (! $this->hasCoordinates()) {
            return null;
        }

        return "https://maps.google.com/?q={$this->latitude},{$this->longitude}";
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->logo);
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->cover_image);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('organisasi')
            ->logOnly(['name', 'slug', 'type', 'status', 'logo', 'cover_image', 'is_featured', 'order'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
