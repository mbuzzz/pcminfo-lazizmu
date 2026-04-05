<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PageStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'excerpt',
        'content',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => PageStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isPublished(): bool
    {
        return $this->status === PageStatus::Published
            && ($this->published_at === null || $this->published_at->isPast());
    }

    protected function content(): Attribute
    {
        return Attribute::make(
            set: static fn (?string $value): ?string => static::sanitizeHtml($value),
        );
    }

    private static function sanitizeHtml(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $html = preg_replace('#<\s*(script|style|iframe|object|embed)[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html);
        $html = preg_replace('#\son\w+\s*=\s*(["\"]).*?\1#is', '', $html);
        $html = preg_replace('#(href|src)\s*=\s*(["\"])\s*javascript:.*?\2#is', '$1=$2#$2', $html);

        return trim((string) $html);
    }
}
