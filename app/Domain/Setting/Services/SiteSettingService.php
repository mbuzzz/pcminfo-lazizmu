<?php

declare(strict_types=1);

namespace App\Domain\Setting\Services;

use App\Domain\Setting\Enums\SettingGroupEnum;
use App\Services\Media\MediaUploadService;
use Illuminate\Support\Arr;

final class SiteSettingService
{
    /**
     * @var list<string>
     */
    private const FILE_KEYS = [
        'logo',
        'favicon',
        'qris_image',
        'default_og_image',
    ];

    public function __construct(
        private readonly SettingService $settingService,
        private readonly MediaUploadService $mediaUploadService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return array_replace($this->defaults(), $this->settingService->group(SettingGroupEnum::App));
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function getWithDefault(string $key): mixed
    {
        return $this->get($key, $this->defaults()[$key] ?? null);
    }

    public function siteName(): string
    {
        return (string) $this->getWithDefault('site_name');
    }

    public function logoUrl(): ?string
    {
        return $this->mediaUploadService->url($this->stringValue('logo'));
    }

    public function faviconUrl(): ?string
    {
        return $this->mediaUploadService->url($this->stringValue('favicon'));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(array $data): void
    {
        $current = $this->all();
        $payload = $this->normalize($data, $current);

        $this->cleanupReplacedFiles($current, $payload);

        $this->settingService->putMany(SettingGroupEnum::App, $payload, isPublic: true);
    }

    /**
     * @return array<string, mixed>
     */
    public function donationFallbacks(): array
    {
        return [
            'qris_image' => $this->getWithDefault('qris_image'),
            'donation_whatsapp_number' => $this->getWithDefault('donation_whatsapp_number'),
            'donation_whatsapp_message_template' => $this->getWithDefault('donation_whatsapp_message_template'),
            'donation_instruction_text' => $this->getWithDefault('donation_instruction_text'),
        ];
    }

    /**
     * @return array<string, string|null>
     */
    public function seoDefaults(): array
    {
        return [
            'title' => $this->getWithDefault('default_meta_title'),
            'description' => $this->getWithDefault('default_meta_description'),
            'og_image' => $this->getWithDefault('default_og_image'),
        ];
    }

    /**
     * @return array{badge: string|null, title: string|null, description: string|null}
     */
    public function homepageFeature(): array
    {
        return [
            'badge' => $this->getWithDefault('homepage_feature_badge'),
            'title' => $this->getWithDefault('homepage_feature_title'),
            'description' => $this->getWithDefault('homepage_feature_description'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'site_name' => config('app.name', 'Portal Digital PCM Genteng'),
            'site_tagline' => null,
            'site_description' => null,
            'logo' => null,
            'favicon' => null,
            'email' => null,
            'phone' => null,
            'whatsapp_number' => null,
            'address' => null,
            'google_maps_url' => null,
            'instagram' => null,
            'facebook' => null,
            'youtube' => null,
            'tiktok' => null,
            'qris_image' => null,
            'donation_whatsapp_number' => null,
            'donation_whatsapp_message_template' => 'Assalamualaikum, saya ingin melakukan konfirmasi donasi untuk campaign :campaign_title.',
            'donation_instruction_text' => null,
            'default_meta_title' => config('app.name', 'Portal Digital PCM Genteng'),
            'default_meta_description' => null,
            'default_og_image' => null,
            'site_verification_code' => null,
            'footer_description' => null,
            'footer_copyright' => '© ' . now()->year . ' Portal Digital PCM Genteng & Lazismu',
            'footer_links' => [],
            'primary_color' => '#b45309',
            'secondary_color' => '#1f2937',
            'accent_color' => '#15803d',
            'default_cta_text' => 'Donasi Sekarang',
            'homepage_feature_badge' => 'Nilai Gerakan',
            'homepage_feature_title' => 'Amanah yang ditampilkan secara terbuka akan lebih mudah dipercaya, diikuti, dan didukung bersama.',
            'homepage_feature_description' => 'Portal ini dirancang agar publik bisa membaca, mengikuti, dan menyalurkan dukungan tanpa hambatan teknis yang rumit.',
            'donasi_cepat_title' => 'Dukung gerakan yang lebih transparan dan mudah dijangkau.',
            'donasi_cepat_description' => 'Gunakan QRIS global, lanjutkan ke program spesifik, atau konfirmasi langsung melalui WhatsApp organisasi.',
            'ekosistem_gerakan_badge' => 'Ekosistem Gerakan',
            'ekosistem_gerakan_title' => null,
            'ekosistem_gerakan_description' => null,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $current
     * @return array<string, mixed>
     */
    private function normalize(array $data, array $current): array
    {
        $payload = array_replace($this->defaults(), $current, $data);

        foreach ($payload as $key => $value) {
            if (is_string($value)) {
                $payload[$key] = trim($value) !== '' ? trim($value) : null;
            }
        }

        $payload['footer_links'] = collect(Arr::wrap($payload['footer_links']))
            ->map(static function (mixed $item): ?array {
                if (! is_array($item)) {
                    return null;
                }

                $label = trim((string) ($item['label'] ?? ''));
                $url = trim((string) ($item['url'] ?? ''));

                if ($label === '' || $url === '') {
                    return null;
                }

                return [
                    'label' => $label,
                    'url' => $url,
                ];
            })
            ->filter()
            ->values()
            ->all();

        return $payload;
    }

    /**
     * @param array<string, mixed> $current
     * @param array<string, mixed> $payload
     */
    private function cleanupReplacedFiles(array $current, array $payload): void
    {
        foreach (self::FILE_KEYS as $key) {
            $oldPath = $current[$key] ?? null;
            $newPath = $payload[$key] ?? null;

            if (blank($oldPath)) {
                continue;
            }

            if (blank($newPath) || $oldPath !== $newPath) {
                $this->mediaUploadService->delete((string) $oldPath);
            }
        }
    }

    private function stringValue(string $key): ?string
    {
        $value = $this->get($key);

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return trim($value);
    }
}
