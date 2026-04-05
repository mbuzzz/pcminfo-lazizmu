<?php

declare(strict_types=1);

namespace Tests\Feature\Frontend;

use App\Domain\Setting\Enums\SettingGroupEnum;
use App\Domain\Setting\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PublicSiteSettingsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_frontend_uses_site_settings_data_from_database(): void
    {
        app(SettingService::class)->putMany(SettingGroupEnum::App, [
            'site_name' => 'Portal Digital PCM Genteng',
            'site_tagline' => 'Pusat informasi dan layanan donasi',
            'site_description' => 'Website resmi PCM Genteng dan Lazismu.',
            'logo' => 'settings/site/logo.png',
            'favicon' => 'settings/site/favicon.ico',
            'email' => 'admin@example.test',
            'phone' => '0333-123456',
            'whatsapp_number' => '6281234567890',
            'instagram' => 'https://instagram.com/pcmgenteng',
            'footer_description' => 'Footer portal resmi organisasi.',
            'footer_copyright' => '© 2026 PCM Genteng',
            'footer_links' => [
                ['label' => 'Profil', 'url' => 'https://example.test/profil'],
            ],
            'qris_image' => 'settings/donation/qris.png',
            'donation_whatsapp_number' => '6281987654321',
            'donation_instruction_text' => 'Silakan transfer lalu kirim konfirmasi.',
            'default_meta_title' => 'Portal Resmi PCM Genteng',
            'default_meta_description' => 'Deskripsi global website.',
            'default_og_image' => 'settings/seo/default-og.png',
            'primary_color' => '#112233',
            'secondary_color' => '#334455',
            'accent_color' => '#556677',
            'default_cta_text' => 'Salurkan Donasi',
            'homepage_feature_badge' => 'Pesan Utama',
            'homepage_feature_title' => 'Kolaborasi organisasi yang lebih terbuka.',
            'homepage_feature_description' => 'Pesan utama homepage diambil dari site settings.',
        ], isPublic: true);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Portal Digital PCM Genteng');
        $response->assertSee('Pusat informasi dan layanan donasi');
        $response->assertSee('admin@example.test');
        $response->assertSee('https://instagram.com/pcmgenteng', escape: false);
        $response->assertSee('/storage/settings/site/favicon.ico', escape: false);
        $response->assertSee('/storage/settings/donation/qris.png', escape: false);
        $response->assertSee('Salurkan Donasi');
        $response->assertSee('https://example.test/profil', escape: false);
        $response->assertSee('Website resmi PCM Genteng dan Lazismu.');
        $response->assertSee('--site-primary: #112233', escape: false);
        $response->assertSee('Pesan Utama');
        $response->assertSee('Kolaborasi organisasi yang lebih terbuka.');
        $response->assertSee('Pesan utama homepage diambil dari site settings.');
    }
}
