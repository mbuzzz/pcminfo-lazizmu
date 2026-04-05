<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription">
    @php
        $identity = $siteSettings->identity();
        $contact = $siteSettings->contact();
        $social = $siteSettings->social();
    @endphp

    <section class="mx-auto max-w-6xl space-y-8 px-4 py-8 md:px-6 md:py-12">
        <div class="space-y-3 rounded-[32px] border border-white/60 bg-white/90 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)] md:p-8">
            <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">
                <x-public.ui.icon name="contact-round" class="h-4 w-4" />
                <span>Kontak Resmi</span>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-slate-950 md:text-5xl">{{ $identity['name'] }}</h1>
            <p class="max-w-3xl text-sm leading-7 text-slate-600 md:text-base">{{ $identity['description'] ?: 'Gunakan informasi kontak resmi berikut untuk komunikasi, konfirmasi, atau kebutuhan koordinasi lebih lanjut.' }}</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_1fr]">
            <div class="space-y-4 rounded-[32px] border border-white/60 bg-white/90 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)] md:p-8">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Informasi Kontak</div>

                @if ($contact['email'])
                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700"><span class="inline-flex items-center gap-2"><x-public.ui.icon name="mail" class="h-4 w-4" /> {{ $contact['email'] }}</span></div>
                @endif
                @if ($contact['phone'])
                    <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700"><span class="inline-flex items-center gap-2"><x-public.ui.icon name="phone" class="h-4 w-4" /> {{ $contact['phone'] }}</span></div>
                @endif
                @if ($contact['whatsapp_number'])
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact['whatsapp_number']) }}" target="_blank" rel="noreferrer" class="block rounded-2xl bg-slate-50 p-4 text-sm text-slate-700 transition hover:bg-slate-100">
                        <span class="inline-flex items-center gap-2"><x-public.ui.icon name="message-circle" class="h-4 w-4" /> {{ $contact['whatsapp_number'] }}</span>
                    </a>
                @endif
                @if ($contact['address'])
                    <div class="rounded-2xl bg-slate-50 p-4 text-sm leading-7 text-slate-700"><span class="inline-flex items-start gap-2"><x-public.ui.icon name="map-pin" class="mt-1 h-4 w-4" /> <span>{{ $contact['address'] }}</span></span></div>
                @endif
            </div>

            <div class="space-y-4 rounded-[32px] border border-white/60 bg-white/90 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)] md:p-8">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Sosial & Lokasi</div>

                <x-public.settings.social-links :links="$social" />

                @if ($contact['google_maps_url'])
                    <x-public.ui.button :href="$contact['google_maps_url']" target="_blank" rel="noreferrer" variant="secondary" icon="map">
                        Buka Google Maps
                    </x-public.ui.button>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
