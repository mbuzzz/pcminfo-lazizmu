<?php

namespace App\Models;

use App\Enums\LeaderOrganization;
use App\Models\Concerns\HasMediaUrl;
use App\Services\Media\MediaUploadService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Leader / E-Struktur Model
 *
 * `user_id` sengaja nullable — pengurus PCM tidak harus punya akun portal.
 * Snapshot data personal disimpan di model ini agar bisa tampil publik
 * tanpa mengekspos tabel users.
 * Ordering via `order` + `position_level` enum mengatur tampilan e-struktur.
 */
class Leader extends Model
{
    use HasMediaUrl;
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'institution_id',
        'name',
        'photo',
        'position',
        'division',
        'nbm',
        'organization',
        'position_level',
        'period',
        'phone',
        'email',
        'bio',
        'status',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'organization' => LeaderOrganization::class,
            'order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (Leader $leader): void {
            app(MediaUploadService::class)->delete($leader->photo);
        });
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /** Filter berdasarkan periodesasi, mis. "2022-2027" */
    public function scopeForPeriod(Builder $query, string $period): Builder
    {
        return $query->where('period', $period);
    }

    public function scopeOfOrganization(Builder $query, LeaderOrganization|string $org): Builder
    {
        return $query->where(
            'organization',
            $org instanceof LeaderOrganization ? $org->value : $org
        );
    }

    /** Urutan untuk tampilan e-struktur: leadership → vice → secretary → treasurer → member */
    public function scopeStructureOrder(Builder $query): Builder
    {
        return $query->orderByRaw("FIELD(position_level, 'leadership','vice','secretary','treasurer','member')")
            ->orderBy('order');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->mediaUrl($this->photo);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('organisasi')
            ->logOnly(['name', 'photo', 'position', 'division', 'organization', 'position_level', 'period', 'status', 'order'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
