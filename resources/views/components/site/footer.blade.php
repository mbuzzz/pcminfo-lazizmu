@php
    $identity = $siteSettings->identity();
    $contact = $siteSettings->contact();
    $social = $siteSettings->social();
    $footer = $siteSettings->footer();
@endphp

<footer class="border-t border-slate-200 bg-slate-950 text-slate-100">
    <div class="mx-auto grid max-w-6xl gap-10 px-6 py-14 md:grid-cols-3">
        <div class="space-y-4">
            <div class="text-lg font-semibold">{{ $identity['name'] }}</div>

            @if ($footer['description'])
                <p class="max-w-md text-sm leading-6 text-slate-300">
                    {{ $footer['description'] }}
                </p>
            @endif
        </div>

        <div class="space-y-4">
            <div class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Kontak</div>

            <div class="space-y-2 text-sm text-slate-300">
                @if ($contact['email'])
                    <div>{{ $contact['email'] }}</div>
                @endif

                @if ($contact['phone'])
                    <div>{{ $contact['phone'] }}</div>
                @endif

                @if ($contact['address'])
                    <div>{{ $contact['address'] }}</div>
                @endif

                @if ($contact['google_maps_url'])
                    <a href="{{ $contact['google_maps_url'] }}" target="_blank" rel="noreferrer" class="inline-block text-white underline underline-offset-4">
                        Lihat Lokasi
                    </a>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Tautan</div>

            <div class="flex flex-col gap-2 text-sm text-slate-300">
                @foreach ($footer['links'] as $link)
                    <a href="{{ $link['url'] }}" class="hover:text-white">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="flex flex-wrap gap-3 pt-2 text-sm">
                @foreach ($social as $platform => $url)
                    @if ($url)
                        <a href="{{ $url }}" target="_blank" rel="noreferrer" class="text-slate-300 hover:text-white">
                            {{ ucfirst($platform) }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="border-t border-slate-800 px-6 py-4 text-center text-sm text-slate-400">
        {{ $footer['copyright'] }}
    </div>
</footer>
