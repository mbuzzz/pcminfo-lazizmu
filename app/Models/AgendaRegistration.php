<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AgendaRegistration / Pendaftaran Peserta Kegiatan
 *
 * `registration_code` dipakai untuk check-in via QR code di acara.
 * UNIQUE (agenda_id, email) mencegah 1 email mendaftar 2x per event.
 * `meta` JSON menyimpan data form tambahan (ukuran baju, asal instansi, dll.)
 * yang berbeda per event tanpa perlu alter tabel.
 */
class AgendaRegistration extends Model
{
    protected $table = 'agenda_registrations';

    protected $fillable = [
        'agenda_id',
        'user_id',
        'name',
        'email',
        'phone',
        'institution_name',
        'registration_code',
        'status',
        'notes',
        'admin_notes',
        'meta',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'checked_in_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Agenda::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeAttended(Builder $query): Builder
    {
        return $query->where('status', 'attended');
    }

    public function scopeCheckedIn(Builder $query): Builder
    {
        return $query->whereNotNull('checked_in_at');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isAttended(): bool
    {
        return $this->status === 'attended' || $this->checked_in_at !== null;
    }

    public function getMeta(string $key, mixed $default = null): mixed
    {
        return data_get($this->meta, $key, $default);
    }
}
