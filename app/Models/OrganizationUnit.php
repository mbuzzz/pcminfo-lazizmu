<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrganizationUnitType;
use App\Models\Concerns\HasMediaUrl;
use App\Services\Media\MediaUploadService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OrganizationUnit extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationUnitFactory> */
    use HasFactory;
    use HasMediaUrl;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'slug',
        'acronym',
        'tagline',
        'description',
        'logo',
        'chairperson',
        'secretary',
        'phone',
        'email',
        'website',
        'address',
        'meta',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => OrganizationUnitType::class,
            'meta' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::forceDeleted(function (OrganizationUnit $organizationUnit): void {
            app(MediaUploadService::class)->delete($organizationUnit->logo);
        });
    }

    public function scopeOfType(Builder $query, OrganizationUnitType|string $type): Builder
    {
        return $query->where('type', $type instanceof OrganizationUnitType ? $type->value : $type);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->logo);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('organisasi')
            ->logOnly(['type', 'name', 'slug', 'logo', 'chairperson', 'secretary', 'is_active', 'sort_order'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
