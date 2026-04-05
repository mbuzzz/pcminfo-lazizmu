<?php

declare(strict_types=1);

namespace Tests\Feature\Frontend;

use App\Domain\Setting\Services\SiteSettingService;
use App\Models\Agenda;
use App\Models\Campaign;
use App\Models\Distribution;
use App\Models\Institution;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoPortalSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_portal_seeder_mengisi_data_publik_utama(): void
    {
        $this->seed(\Database\Seeders\DemoPortalSeeder::class);

        $this->assertSame('PCM Genteng & Lazismu', app(SiteSettingService::class)->siteName());
        $this->assertGreaterThanOrEqual(4, Post::query()->count());
        $this->assertGreaterThanOrEqual(4, Agenda::query()->count());
        $this->assertGreaterThanOrEqual(4, Campaign::query()->count());
        $this->assertGreaterThanOrEqual(4, Institution::query()->count());
        $this->assertGreaterThanOrEqual(3, Distribution::query()->count());
    }
}
