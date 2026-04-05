<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Domain\Setting\Enums\SettingGroupEnum;
use App\Domain\Setting\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminPanelBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_uses_site_settings_for_branding(): void
    {
        app(SettingService::class)->putMany(SettingGroupEnum::App, [
            'site_name' => 'Portal Digital PCM Genteng',
            'logo' => 'settings/site/logo.png',
            'favicon' => 'settings/site/favicon.ico',
        ], isPublic: true);

        $response = $this->get('/admin/login');

        $response->assertOk();
        $response->assertSee('Portal Digital PCM Genteng');
        $response->assertSee('/storage/settings/site/logo.png', escape: false);
        $response->assertSee('/storage/settings/site/favicon.ico', escape: false);
    }
}
