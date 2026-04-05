<?php

declare(strict_types=1);

namespace Tests\Feature\Frontend;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPwaAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manifest_web_dapat_diakses(): void
    {
        $response = $this->get(route('pwa.manifest'));

        $response
            ->assertOk()
            ->assertHeader('content-type', 'application/manifest+json')
            ->assertJsonStructure([
                'name',
                'short_name',
                'start_url',
                'display',
                'theme_color',
                'icons',
            ]);
    }

    public function test_service_worker_dapat_diakses(): void
    {
        $response = $this->get(route('pwa.service-worker'));

        $response
            ->assertOk()
            ->assertHeader('content-type', 'application/javascript; charset=UTF-8')
            ->assertSee('CACHE_NAME', false)
            ->assertSee('self.addEventListener', false);
    }
}
