<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Domain\Setting\Services\SiteSettingService;
use App\Models\Concerns\HasMediaUrl;
use App\Services\Media\MediaUploadService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Post / Berita & Artikel Model
 *
 * Mendukung editorial workflow: draft → review → published → archived.
 * `published_at` terpisah dari `created_at` untuk scheduled publishing.
 * `view_count` denormalized — update via controller/middleware increment.
 */
class Post extends Model
{
    use HasMediaUrl;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'author_id',
        'institution_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'type',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'view_count',
        'is_featured',
        'allow_comments',
    ];

    protected function casts(): array
    {
        return [
            'type' => PostType::class,
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'view_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::forceDeleted(function (Post $post): void {
            app(MediaUploadService::class)->delete($post->featured_image);
        });
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class)->latest();
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(PostReaction::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /** Hanya konten yang sudah terbit dan waktu publish sudah lewat */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Draft);
    }

    public function scopePendingReview(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Review);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfType(Builder $query, PostType|string $type): Builder
    {
        return $query->where('type', $type instanceof PostType ? $type->value : $type);
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('published_at', 'desc');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isPublished(): bool
    {
        return $this->status === PostStatus::Published
            && ($this->published_at === null || $this->published_at->isPast());
    }

    /** Increment view — panggil dari controller/middleware */
    public function incrementView(): void
    {
        $this->increment('view_count');
    }

    /** SEO title: pakai meta_title jika ada, fallback ke title */
    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title
            ?: $this->title
            ?: (string) app(SiteSettingService::class)->getWithDefault('default_meta_title');
    }

    /** SEO description: pakai meta_description jika ada, fallback ke excerpt */
    public function getSeoDescriptionAttribute(): ?string
    {
        return $this->meta_description
            ?: $this->excerpt
            ?: app(SiteSettingService::class)->getWithDefault('default_meta_description');
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->featured_image);
    }

    public function getSeoImageUrlAttribute(): ?string
    {
        return $this->featured_image_url
            ?: app(MediaUploadService::class)->url(
                app(SiteSettingService::class)->getWithDefault('default_og_image')
            );
    }

    public function getSeoMetaAttribute(): array
    {
        return [
            'title' => $this->seo_title,
            'description' => Str::limit(strip_tags((string) ($this->seo_description ?? '')), 160, ''),
            'image' => $this->seo_image_url,
        ];
    }
}
