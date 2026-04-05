<?php

namespace App\Models;

use App\Enums\DonationPaymentMethod;
use App\Enums\DonationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Donation Model
 *
 * Status utama mengikuti verifikasi admin.
 *
 * `meta` JSON menyimpan raw payload dari payment gateway sehingga integrasi
 * Midtrans/Xendit/Tripay tidak perlu alter tabel — cukup extend meta schema.
 *
 * Snapshot `donor_*`: data donatur disalin saat donasi dibuat agar tidak
 * berubah jika user edit profil atau akun dihapus.
 *
 * `transaction_code` diisi sebelum save (lihat DonationObserver atau
 * Service generateTransactionCode).
 */
class Donation extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'transaction_code',
        'payer_name',
        'payer_email',
        'payer_phone',
        'is_anonymous',
        'amount',
        'quantity',
        'unit_label',
        'message',
        'payment_method',
        'payment_channel',
        'status',
        'verified_by',
        'submitted_at',
        'verified_at',
        'rejected_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => DonationPaymentMethod::class,
            'status' => DonationStatus::class,
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'rejected_at' => 'datetime',
            'amount' => 'integer',
            'quantity' => 'integer',
            'is_anonymous' => 'boolean',
            'meta' => 'array',
        ];
    }

    // -------------------------------------------------------------------------
    // Media Library — Bukti Transfer
    // -------------------------------------------------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('transfer_proof')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf']);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [
            DonationStatus::Pending->value,
        ]);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->whereIn('status', [
            DonationStatus::Verified->value,
        ]);
    }

    public function scopeAnonymous(Builder $query): Builder
    {
        return $query->where('is_anonymous', true);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_anonymous', false);
    }

    /** Filter berdasarkan metode pembayaran manual (perlu konfirmasi admin) */
    public function scopeManualPayment(Builder $query): Builder
    {
        return $query->whereIn('payment_method', [
            DonationPaymentMethod::ManualTransfer->value,
            DonationPaymentMethod::Cash->value,
            DonationPaymentMethod::BankTransfer->value,
        ]);
    }

    public function scopeForCampaign(Builder $query, int $campaignId): Builder
    {
        return $query->where('campaign_id', $campaignId);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('verified_at', now()->month)
            ->whereYear('verified_at', now()->year);
    }

    // -------------------------------------------------------------------------
    // Computed Attributes
    // -------------------------------------------------------------------------

    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn (): string => 'Rp '.number_format($this->amount, 0, ',', '.')
        );
    }

    /** Nama tampilan publik (anonim → "Hamba Allah") */
    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->is_anonymous
                ? 'Hamba Allah'
                : ($this->payer_name ?: ($this->user?->name ?? 'Donatur'))
        );
    }

    protected function isVerified(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => in_array($this->status?->value ?? $this->status, [
                DonationStatus::Verified->value,
            ], true)
        );
    }

    // -------------------------------------------------------------------------
    // Helpers — Gateway Meta
    // -------------------------------------------------------------------------

    /**
     * Ambil nilai dari JSON meta payment gateway.
     * Contoh: $donation->getMeta('snap_token')
     */
    public function getMeta(string $key, mixed $default = null): mixed
    {
        return data_get($this->meta, $key, $default);
    }

    /**
     * Set payload gateway satu key tanpa overwrite meta lain.
     */
    public function setMeta(string $key, mixed $value): static
    {
        $meta = $this->meta ?? [];
        data_set($meta, $key, $value);
        $this->meta = $meta;

        return $this;
    }

    /** Apakah donasi ini perlu konfirmasi manual dari admin? */
    public function requiresManualConfirmation(): bool
    {
        return in_array($this->payment_method->value, [
            DonationPaymentMethod::ManualTransfer->value,
            DonationPaymentMethod::Cash->value,
            DonationPaymentMethod::BankTransfer->value,
        ]);
    }

    protected function donorName(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->payer_name,
        );
    }

    protected function donorEmail(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->payer_email,
        );
    }

    protected function donorPhone(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->payer_phone,
        );
    }

    protected function unitQuantity(): Attribute
    {
        return Attribute::make(
            get: fn (): int => (int) $this->quantity,
        );
    }
}
