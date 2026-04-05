<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $pageTitle ?? $siteSettings->siteName() }}</title>

        <x-public.layout.seo-meta
            :title="$pageTitle ?? null"
            :description="$pageDescription ?? null"
            :image="$pageImage ?? null"
        />

        @if ($siteSettings->faviconUrl())
            <link rel="icon" href="{{ $siteSettings->faviconUrl() }}">
        @endif

        <link rel="manifest" href="{{ route('pwa.manifest') }}">
        <meta name="theme-color" content="{{ $siteSettings->theme()['primary_color'] }}">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="{{ $siteSettings->siteName() }}">

        <style>
            :root {
                @foreach ($siteSettings->themeCssVariables() as $variable => $value)
                    {{ $variable }}: {{ $value }};
                @endforeach
            }

            [x-cloak] {
                display: none !important;
            }

            body {
                margin: 0;
                font-family: "Instrument Sans", system-ui, sans-serif;
                background:
                    radial-gradient(circle at top, color-mix(in srgb, var(--site-primary) 12%, white) 0%, rgba(255,255,255,0.98) 36%),
                    radial-gradient(circle at 85% 15%, color-mix(in srgb, var(--site-accent) 16%, white) 0%, rgba(255,255,255,0) 24%),
                    radial-gradient(circle at 15% 25%, color-mix(in srgb, var(--site-secondary) 11%, white) 0%, rgba(255,255,255,0) 30%),
                    linear-gradient(180deg, #fff 0%, #f8fafc 100%);
                color: #0f172a;
                overflow-x: hidden;
            }
        </style>

        @livewireStyles

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen antialiased selection:bg-[color-mix(in_srgb,var(--site-primary)_18%,white)] selection:text-slate-950">
        <a href="#konten-utama" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-full focus:bg-slate-950 focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
            Lewati ke konten utama
        </a>
        <div class="hidden lg:block">
            <x-public.layout.header />
        </div>

        <main id="konten-utama" class="pb-28 lg:pb-0">
            {{ $slot }}
        </main>

        <x-public.layout.footer />
        <x-public.layout.mobile-bottom-nav />
        @livewireScripts
        @stack('scripts')
    </body>
</html>
