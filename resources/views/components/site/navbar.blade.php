@php
    $identity = $siteSettings->identity();
    $contact = $siteSettings->contact();
@endphp

<header class="border-b border-slate-200/80 bg-white/90 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-6 py-4">
        <div class="flex items-center gap-4">
            @if ($identity['logo_url'])
                <img
                    src="{{ $identity['logo_url'] }}"
                    alt="Logo {{ $identity['name'] }}"
                    class="h-12 w-12 rounded-xl object-cover ring-1 ring-slate-200"
                >
            @else
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl text-sm font-bold text-white"
                    style="background-color: var(--site-primary);"
                >
                    {{ \Illuminate\Support\Str::of($identity['name'])->substr(0, 2)->upper() }}
                </div>
            @endif

            <div class="space-y-1">
                <div class="text-base font-semibold text-slate-900">{{ $identity['name'] }}</div>

                @if ($identity['tagline'])
                    <div class="text-sm text-slate-600">{{ $identity['tagline'] }}</div>
                @endif
            </div>
        </div>

        <div class="hidden items-center gap-3 md:flex">
            @if ($contact['whatsapp_number'])
                <a
                    href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact['whatsapp_number']) }}"
                    target="_blank"
                    rel="noreferrer"
                    class="rounded-full px-4 py-2 text-sm font-medium text-white"
                    style="background-color: var(--site-accent);"
                >
                    Hubungi WhatsApp
                </a>
            @endif
        </div>
    </div>
</header>
