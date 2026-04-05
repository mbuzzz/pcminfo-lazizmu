<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Domain\Setting\Support\PublicSiteSettings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

final class WebManifestController extends Controller
{
    public function __invoke(PublicSiteSettings $siteSettings): Response
    {
        $identity = $siteSettings->identity();
        $theme = $siteSettings->theme();

        $icons = [
            ['src' => '/icons/icon-72x72.png', 'sizes' => '72x72', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ['src' => '/icons/icon-96x96.png', 'sizes' => '96x96', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ['src' => '/icons/icon-128x128.png', 'sizes' => '128x128', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ['src' => '/icons/icon-144x144.png', 'sizes' => '144x144', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ['src' => '/icons/icon-152x152.png', 'sizes' => '152x152', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ['src' => '/icons/icon-192x192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ['src' => '/icons/icon-384x384.png', 'sizes' => '384x384', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ['src' => '/icons/icon-512x512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
        ];

        return response()->make(json_encode([
            'name' => $identity['name'],
            'short_name' => $identity['name'],
            'description' => $identity['description'] ?: $identity['tagline'],
            'start_url' => route('home'),
            'scope' => url('/'),
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => $theme['primary_color'],
            'icons' => $icons,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 200, [
            'Content-Type' => 'application/manifest+json',
        ]);
    }
}
