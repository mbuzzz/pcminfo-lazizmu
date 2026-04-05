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

        $icons = collect([
            $identity['favicon_url'],
            $identity['logo_url'],
        ])
            ->filter()
            ->unique()
            ->values()
            ->map(static fn (string $src): array => [
                'src' => $src,
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any',
            ])
            ->all();

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
