@php
    $identity = $siteSettings->identity();
    $contact = $siteSettings->contact();
    $social = $siteSettings->social();
    $footer = $siteSettings->footer();
    $donation = $siteSettings->donation();
@endphp

<footer class="mt-16 bg-slate-950 text-slate-100 md:mt-24">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 md:grid-cols-[1.2fr_0.8fr_0.8fr] md:px-6 md:py-16">
        <div class="space-y-5">
            <div class="flex items-center gap-3">
                @if ($identity['logo_url'])
                    <img src="{{ $identity['logo_url'] }}" alt="Logo {{ $identity['name'] }}" class="h-12 w-12 rounded-2xl object-cover">
                @endif
                <div>
                    <div class="text-lg font-bold">{{ $identity['name'] }}</div>
                    @if ($identity['tagline'])
                        <div class="text-sm text-slate-400">{{ $identity['tagline'] }}</div>
                    @endif
                </div>
            </div>

            @if ($footer['description'])
                <p class="max-w-xl text-sm leading-7 text-slate-300">{{ $footer['description'] }}</p>
            @endif

            <x-public.ui.button :href="route('campaigns.index')" variant="donation" icon="badge-cent">
                {{ $donation['default_cta_text'] }}
            </x-public.ui.button>
        </div>

        <div class="space-y-4">
            <div class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Kontak</div>
            <div class="space-y-2 text-sm text-slate-300">
                @if ($contact['email']) <div class="flex items-start gap-3"><x-public.ui.icon name="mail" class="mt-0.5 h-4 w-4 text-slate-500" /> <span>{{ $contact['email'] }}</span></div> @endif
                @if ($contact['phone']) <div class="flex items-start gap-3"><x-public.ui.icon name="phone" class="mt-0.5 h-4 w-4 text-slate-500" /> <span>{{ $contact['phone'] }}</span></div> @endif
                @if ($contact['whatsapp_number']) <div class="flex items-start gap-3"><x-public.ui.icon name="message-circle" class="mt-0.5 h-4 w-4 text-slate-500" /> <span>{{ $contact['whatsapp_number'] }}</span></div> @endif
                @if ($contact['address']) <div class="flex items-start gap-3"><x-public.ui.icon name="map-pin" class="mt-0.5 h-4 w-4 text-slate-500" /> <span>{{ $contact['address'] }}</span></div> @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Jelajah</div>
            <div class="flex flex-col gap-2">
                <a href="{{ route('pages.about') }}" class="inline-flex items-center gap-2 text-sm text-slate-300 transition hover:text-white"><x-public.ui.icon name="info" class="h-4 w-4" /> Tentang</a>
                <a href="{{ route('pages.contact') }}" class="inline-flex items-center gap-2 text-sm text-slate-300 transition hover:text-white"><x-public.ui.icon name="contact-round" class="h-4 w-4" /> Kontak</a>
            </div>
            <x-public.settings.footer-links :links="$footer['links']" />
            <x-public.settings.social-links :links="$social" />
        </div>
    </div>

    <div class="border-t border-slate-800 px-4 py-4 text-center text-sm text-slate-400 md:px-6">
        {{ $footer['copyright'] }}
    </div>
</footer>
