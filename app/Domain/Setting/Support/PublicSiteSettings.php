<?php

declare(strict_types=1);

namespace App\Domain\Setting\Support;

use App\Application\Contracts\StorageUrlGenerator;
use App\Domain\Setting\Services\SiteSettingService;
use Illuminate\Support\Arr;

final class PublicSiteSettings
{
    public function __construct(
        private readonly SiteSettingService $siteSettingService,
        private readonly StorageUrlGenerator $storageUrlGenerator,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->siteSettingService->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function identity(): array
    {
        return [
            'name' => $this->siteName(),
            'tagline' => $this->siteTagline(),
            'description' => $this->siteDescription(),
            'logo_url' => $this->logoUrl(),
            'favicon_url' => $this->faviconUrl(),
            'ekosistem_gerakan_badge' => $this->stringOrNull('ekosistem_gerakan_badge'),
            'ekosistem_gerakan_title' => $this->stringOrNull('ekosistem_gerakan_title'),
            'ekosistem_gerakan_description' => $this->stringOrNull('ekosistem_gerakan_description'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function contact(): array
    {
        return [
            'email' => $this->value('email'),
            'phone' => $this->value('phone'),
            'whatsapp_number' => $this->value('whatsapp_number'),
            'address' => $this->value('address'),
            'google_maps_url' => $this->value('google_maps_url'),
        ];
    }

    /**
     * @return array<string, string|null>
     */
    public function social(): array
    {
        return [
            'instagram' => $this->value('instagram'),
            'facebook' => $this->value('facebook'),
            'youtube' => $this->value('youtube'),
            'tiktok' => $this->value('tiktok'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function donation(): array
    {
        return [
            'qris_image_url' => $this->storageUrl($this->value('qris_image')),
            'whatsapp_number' => $this->value('donation_whatsapp_number') ?: $this->value('whatsapp_number'),
            'whatsapp_message_template' => $this->value('donation_whatsapp_message_template'),
            'instruction_text' => $this->value('donation_instruction_text'),
            'default_cta_text' => $this->defaultCtaText(),
            'donasi_cepat_title' => $this->stringOrNull('donasi_cepat_title'),
            'donasi_cepat_description' => $this->stringOrNull('donasi_cepat_description'),
        ];
    }

    /**
     * @return array{badge: string|null, title: string|null, description: string|null}
     */
    public function homepageFeature(): array
    {
        return [
            'badge' => $this->stringOrNull('homepage_feature_badge'),
            'title' => $this->stringOrNull('homepage_feature_title'),
            'description' => $this->stringOrNull('homepage_feature_description'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function footer(): array
    {
        return [
            'description' => $this->value('footer_description') ?: $this->siteDescription(),
            'copyright' => $this->value('footer_copyright'),
            'links' => $this->footerLinks(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function theme(): array
    {
        return [
            'primary_color' => $this->primaryColor(),
            'secondary_color' => $this->secondaryColor(),
            'accent_color' => $this->accentColor(),
            'default_cta_text' => $this->defaultCtaText(),
            'css_variables' => $this->themeCssVariables(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function seo(?string $title = null, ?string $description = null, ?string $imageUrl = null): array
    {
        $defaults = $this->siteSettingService->seoDefaults();

        $finalTitle = $title ?: ($defaults['title'] ?: $this->siteName());
        $finalDescription = $description ?: ($defaults['description'] ?: $this->siteDescription());
        $finalImageUrl = $imageUrl ?: $this->storageUrl($defaults['og_image'] ?? null);

        return [
            'title' => $finalTitle,
            'description' => $finalDescription,
            'image' => $finalImageUrl,
            'verification_code' => $this->value('site_verification_code'),
        ];
    }

    /**
     * @return array<int, array{label: string, url: string}>
     */
    public function footerLinks(): array
    {
        $links = Arr::wrap($this->value('footer_links', []));

        return collect($links)
            ->filter(static fn (mixed $item): bool => is_array($item))
            ->map(static fn (array $item): array => [
                'label' => (string) ($item['label'] ?? ''),
                'url' => (string) ($item['url'] ?? ''),
            ])
            ->filter(static fn (array $item): bool => $item['label'] !== '' && $item['url'] !== '')
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function themeCssVariables(): array
    {
        return [
            '--site-primary' => $this->primaryColor(),
            '--site-secondary' => $this->secondaryColor(),
            '--site-accent' => $this->accentColor(),
        ];
    }

    public function siteName(): string
    {
        return (string) $this->value('site_name', config('app.name', 'Portal Digital PCM Genteng'));
    }

    public function siteTagline(): ?string
    {
        return $this->stringOrNull('site_tagline');
    }

    public function siteDescription(): ?string
    {
        return $this->stringOrNull('site_description');
    }

    public function logoUrl(): ?string
    {
        return $this->storageUrl($this->value('logo'));
    }

    public function faviconUrl(): ?string
    {
        return $this->storageUrl($this->value('favicon'));
    }

    public function primaryColor(): string
    {
        return (string) $this->value('primary_color', '#b45309');
    }

    public function secondaryColor(): string
    {
        return (string) $this->value('secondary_color', '#1f2937');
    }

    public function accentColor(): string
    {
        return (string) $this->value('accent_color', '#15803d');
    }

    public function defaultCtaText(): string
    {
        return (string) $this->value('default_cta_text', 'Donasi Sekarang');
    }

    public function value(string $key, mixed $default = null): mixed
    {
        return $this->siteSettingService->get($key, $default);
    }

    private function stringOrNull(string $key): ?string
    {
        $value = $this->value($key);

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    private function storageUrl(mixed $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        return $this->storageUrlGenerator->url(trim($path));
    }
}
