<?php

declare(strict_types=1);

namespace Tests\Feature\Frontend;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PublicNavigationLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_hidden_di_mobile_tablet_dan_bottom_nav_tetap_dirender(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('class="hidden lg:block"', false);
        $response->assertSee('Portal Digital', false);
        $response->assertSee('aria-label="Donasi"', false);
    }

    public function test_navigation_public_tetap_konsisten_di_halaman_lain(): void
    {
        $response = $this->get(route('posts.index'));

        $response->assertOk();
        $response->assertSee('href="' . route('home') . '"', false);
        $response->assertSee('href="' . route('posts.index') . '"', false);
        $response->assertSee('href="' . route('agendas.index') . '"', false);
        $response->assertSee('href="' . route('campaigns.index') . '"', false);
        $response->assertSee('href="' . route('pages.about') . '"', false);
        $response->assertSee('aria-label="Cari"', false);
    }
}
