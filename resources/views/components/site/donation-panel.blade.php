@php
    $donation = $siteSettings->donation();
@endphp

<section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-4 text-lg font-semibold text-slate-900">Dukungan Donasi</div>

    @if ($donation['instruction_text'])
        <p class="mb-4 text-sm leading-6 text-slate-600">
            {{ $donation['instruction_text'] }}
        </p>
    @endif

    @if ($donation['qris_image_url'])
        <img
            src="{{ $donation['qris_image_url'] }}"
            alt="QRIS Donasi"
            class="mb-4 w-full rounded-2xl border border-slate-200 object-cover"
        >
    @endif

    @if ($donation['whatsapp_number'])
        <a
            href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $donation['whatsapp_number']) }}"
            target="_blank"
            rel="noreferrer"
            class="inline-flex rounded-full px-5 py-3 text-sm font-semibold text-white"
            style="background-color: var(--site-primary);"
        >
            {{ $donation['default_cta_text'] }}
        </a>
    @endif
</section>
