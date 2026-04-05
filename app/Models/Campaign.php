<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Services\Media\MediaUploadService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Campaign / Program Penggalangan Model
 *
 * Desain config-driven:
 * - `description` (longText): konten rich-text lengkap dari Filament RichEditor
 *   (HTML atau JSON TipTap). Ini konten utama yang tampil di halaman campaign.
 * - `short_description` (text): ringkasan 1-2 kalimat untuk card & OG description.
 * - `config` (JSON): konfigurasi dinamis per type campaign.
 *   Setiap type punya schema config yang berbeda — diakses via getConfig() helper.
 *   Contoh: ZakatFitrah → {cash_equivalent, rice_kg}, Wakaf → {land_area_sqm, price_per_sqm}
 * - `payment_config` (JSON): nomor rekening / QRIS per campaign, override global setting.
 *
 * Progress tracking:
 * - `progress_type = 'amount'` → progress dari collected_amount / target_amount
 * - `progress_type = 'unit'`   → progress dari collected_unit / target_unit
 * - Semua counter diupdate via DonationObserver agar tidak ada race condition.
 *
 * Spatie Media Library untuk featured_image dan galeri foto perkembangan.
 */
class Campaign extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'institution_id',
        'created_by',
        'title',
        'slug',
        'short_description',
        'description',          // Rich text — kompatibel dengan Filament RichEditor / TipTap
        'featured_image',
        'type',
        'status',
        'goal_type',
        'goal_amount',
        'goal_unit',
        'donor_count',
        'progress_type',
        'target_amount',
        'target_unit',
        'unit_label',
        'collected_amount',
        'collected_unit',
        'verified_donor_count',
        'start_date',
        'end_date',
        'published_at',
        'closed_at',
        'config',               // JSON: konfigurasi dinamis per type
        'payment_config',       // JSON: info rekening / gateway per campaign
        'beneficiary_name',
        'beneficiary_description',
        'meta_title',
        'meta_description',
        'is_featured',
        'allow_anonymous',
        'show_donor_list',
    ];

    protected function casts(): array
    {
        return [
            'type' => CampaignType::class,
            'status' => CampaignStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
            'config' => 'array',
            'payment_config' => 'array',
            'goal_amount' => 'integer',
            'goal_unit' => 'integer',
            'donor_count' => 'integer',
            'target_amount' => 'integer',
            'target_unit' => 'integer',
            'collected_amount' => 'integer',
            'collected_unit' => 'integer',
            'verified_donor_count' => 'integer',
            'is_featured' => 'boolean',
            'allow_anonymous' => 'boolean',
            'show_donor_list' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Media Library
    // -------------------------------------------------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('documents'); // RAB, SK, proposal, dll.
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(280)
            ->nonQueued();

        $this->addMediaConversion('hero')
            ->width(1200)
            ->height(630)
            ->nonQueued();
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

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    /** Hanya donasi yang sudah terkonfirmasi */
    public function confirmedDonations(): HasMany
    {
        return $this->hasMany(Donation::class)->where('status', 'verified');
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(Distribution::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(CampaignUpdate::class)->orderBy('published_at', 'desc');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CampaignStatus::Active);
    }

    public function scopePublished(Builder $query): Builder
    {
        // "Publik" = status active atau completed
        return $query->whereIn('status', [
            CampaignStatus::Active->value,
            CampaignStatus::Completed->value,
        ]);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('end_date')
                ->orWhere('end_date', '>=', now()->toDateString());
        });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('end_date')
            ->where('end_date', '<', now()->toDateString());
    }

    public function scopeOfType(Builder $query, CampaignType|string $type): Builder
    {
        return $query->where(
            'type',
            $type instanceof CampaignType ? $type->value : $type
        );
    }

    // -------------------------------------------------------------------------
    // Computed Attributes
    // -------------------------------------------------------------------------

    /**
     * Persentase progress campaign (0-100).
     * Mendukung progress_type amount dan unit.
     */
    protected function progressPercentage(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                if ($this->goal_type === 'nominal') {
                    if (! $this->goal_amount || $this->goal_amount === 0) {
                        return 0;
                    }

                    return min(100, round(($this->collected_amount / $this->goal_amount) * 100, 1));
                }

                if (! $this->goal_unit || $this->goal_unit === 0) {
                    return 0;
                }

                return min(100, round(($this->collected_unit / $this->goal_unit) * 100, 1));
            }
        );
    }

    /** Sisa kebutuhan (nominal) */
    protected function remainingAmount(): Attribute
    {
        return Attribute::make(
            get: fn (): int => max(0, ($this->goal_amount ?? 0) - $this->collected_amount)
        );
    }

    /** Sisa kebutuhan (unit) */
    protected function remainingUnit(): Attribute
    {
        return Attribute::make(
            get: fn (): int => max(0, ($this->goal_unit ?? 0) - $this->collected_unit)
        );
    }

    /** Apakah campaign sudah kadaluarsa? */
    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->end_date !== null && $this->end_date->isPast()
        );
    }

    protected function goalType(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->attributes['goal_type']
                ?? match ($this->attributes['progress_type'] ?? null) {
                    'amount' => 'nominal',
                    'unit' => 'unit',
                    default => $this->attributes['progress_type'] ?? null,
                },
        );
    }

    protected function goalAmount(): Attribute
    {
        return Attribute::make(
            get: fn (): ?int => isset($this->attributes['goal_amount'])
                ? (int) $this->attributes['goal_amount']
                : (isset($this->attributes['target_amount']) ? (int) $this->attributes['target_amount'] : null),
        );
    }

    protected function goalUnit(): Attribute
    {
        return Attribute::make(
            get: fn (): ?int => isset($this->attributes['goal_unit'])
                ? (int) $this->attributes['goal_unit']
                : (isset($this->attributes['target_unit']) ? (int) $this->attributes['target_unit'] : null),
        );
    }

    protected function donorCount(): Attribute
    {
        return Attribute::make(
            get: fn (): int => isset($this->attributes['donor_count'])
                ? (int) $this->attributes['donor_count']
                : (int) ($this->attributes['verified_donor_count'] ?? 0),
        );
    }

    /** Apakah campaign masih bisa menerima donasi? */
    protected function isAcceptingDonations(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->status === CampaignStatus::Active
                                && ! $this->is_expired
        );
    }

    protected function seoTitle(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->meta_title ?: $this->title
        );
    }

    protected function seoDescription(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->meta_description ?: $this->short_description
        );
    }

    protected function featuredImageUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                $mediaUrl = method_exists($this, 'getFirstMediaUrl') ? $this->getFirstMediaUrl('featured_image') : null;

                if (filled($mediaUrl)) {
                    return $mediaUrl;
                }

                return app(MediaUploadService::class)->url($this->featured_image);
            }
        );
    }

    // -------------------------------------------------------------------------
    // Config Helpers — Dynamic per Campaign Type
    // -------------------------------------------------------------------------

    /**
     * Ambil nilai dari JSON config dengan dot-notation.
     *
     * Contoh penggunaan:
     *   $campaign->getConfig('cash_equivalent')       // Zakat Fitrah
     *   $campaign->getConfig('land_area_sqm')         // Wakaf
     *   $campaign->getConfig('scholarship_quota', 10) // Beasiswa
     */
    public function getConfig(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Set nilai config secara dinamis tanpa overwrite seluruh config.
     *
     * Contoh: $campaign->setConfig('rice_kg', 2.5)->save();
     */
    public function setConfig(string $key, mixed $value): static
    {
        $config = $this->config ?? [];
        data_set($config, $key, $value);
        $this->config = $config;

        return $this;
    }

    /**
     * Schema config default per jenis campaign.
     * Berguna untuk pre-fill form di Filament saat type berubah.
     */
    public static function defaultConfigForType(CampaignType $type): array
    {
        return match ($type) {
            CampaignType::ZakatFitrah => [
                'rice_or_cash' => true,
                'cash_equivalent' => 40000,    // Rp per jiwa
                'rice_kg' => 2.5,
            ],
            CampaignType::Wakaf => [
                'land_area_sqm' => null,
                'price_per_sqm' => null,
                'location_detail' => null,
                'certificate_status' => 'process', // process | done
            ],
            CampaignType::Qurban => [
                'animal_type' => 'sapi',       // sapi | kambing | domba
                'share_count' => 7,            // 7 bagian untuk sapi
                'price_per_share' => null,
            ],
            CampaignType::Scholarship => [
                'scholarship_quota' => null,
                'study_level' => null,  // sd | smp | sma | pt
                'monthly_allowance' => null,
                'duration_months' => 12,
            ],
            CampaignType::Emergency => [
                'disaster_type' => null,       // flood | earthquake | fire | other
                'location' => null,
                'affected_people' => null,
            ],
            default => [],
        };
    }

    /**
     * Ambil config rekening pembayaran.
     * Fallback ke setting global jika campaign tidak punya override.
     */
    public function getPaymentConfig(string $key, mixed $default = null): mixed
    {
        return data_get($this->payment_config, $key, $default);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Format collected_amount ke Rupiah */
    public function getFormattedCollectedAmount(): string
    {
        return 'Rp '.number_format($this->collected_amount, 0, ',', '.');
    }

    public function getFormattedGoalAmount(): string
    {
        return 'Rp '.number_format($this->goal_amount ?? 0, 0, ',', '.');
    }
}
