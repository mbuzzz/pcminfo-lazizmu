<?php

declare(strict_types=1);

namespace Tests\Feature\Setting;

use App\Domain\Setting\Enums\SettingGroupEnum;
use App\Domain\Setting\Services\SettingService;
use App\Domain\Setting\Services\SiteSettingService;
use App\Infrastructure\Storage\DefaultStorageUrlGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingMediaUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_setting_service_mengembalikan_value_json_string_tanpa_quote_mentah(): void
    {
        app(SettingService::class)->put(
            SettingGroupEnum::App,
            'logo',
            'settings/site/logo-utama.png',
            isPublic: true,
        );

        $value = app(SettingService::class)->get(SettingGroupEnum::App, 'logo');

        $this->assertSame('settings/site/logo-utama.png', $value);
    }

    public function test_site_setting_service_menghasilkan_url_logo_relatif_untuk_disk_lokal(): void
    {
        app(SettingService::class)->put(
            SettingGroupEnum::App,
            'logo',
            'settings/site/logo-utama.png',
            isPublic: true,
        );

        $this->assertSame(
            '/storage/settings/site/logo-utama.png',
            app(SiteSettingService::class)->logoUrl(),
        );
    }

    public function test_storage_url_generator_menghasilkan_path_relatif_untuk_file_lokal(): void
    {
        $this->assertSame(
            '/storage/settings/site/logo-utama.png',
            app(DefaultStorageUrlGenerator::class)->url('settings/site/logo-utama.png'),
        );
    }
}
