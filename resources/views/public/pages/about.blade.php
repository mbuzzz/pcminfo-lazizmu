<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription">
    @php
        $identity = $siteSettings->identity();
        $footer = $siteSettings->footer();
    @endphp

    <section class="mx-auto max-w-6xl space-y-8 px-4 py-8 md:px-6 md:py-12">
        <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="space-y-6 rounded-[32px] border border-white/60 bg-white/90 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)] md:p-8">
                <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">
                    <x-public.ui.icon name="sparkles" class="h-4 w-4" />
                    <span>Tentang Portal</span>
                </div>

                <div class="space-y-3">
                    <h1 class="text-3xl font-black tracking-tight text-slate-950 md:text-5xl">{{ $identity['name'] }}</h1>
                    @if ($identity['tagline'])
                        <p class="text-lg text-slate-600">{{ $identity['tagline'] }}</p>
                    @endif
                </div>

                <div class="space-y-4 text-sm leading-7 text-slate-600 md:text-base">
                    <p>{{ $identity['description'] ?: 'Portal ini dibangun sebagai ruang digital yang menyatukan informasi organisasi, gerakan dakwah, aktivitas sosial, dan transparansi filantropi PCM Genteng & Lazismu.' }}</p>
                    <p>Fokus utamanya adalah memudahkan publik mengakses berita, agenda, struktur organisasi, amal usaha, program filantropi, dan laporan penyaluran dalam satu pengalaman yang rapi dan mudah dipahami.</p>
                    <p>Kami menempatkan transparansi, kemudahan akses, dan kualitas informasi sebagai dasar utama pengembangan platform ini.</p>
                </div>
            </div>

            <div class="grid gap-4">
                <div class="rounded-[30px] border border-white/60 bg-white/95 p-5 shadow-[0_12px_32px_rgba(15,23,42,0.08)]">
                    <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                        <x-public.ui.icon name="layout-panel-top" class="h-4 w-4" />
                        <span>Ruang Utama</span>
                    </div>
                    <div class="mt-4 grid gap-3 text-sm text-slate-700">
                        <div class="rounded-2xl bg-slate-50 p-4">PCM Info: berita, agenda, amal usaha, dan struktur pimpinan.</div>
                        <div class="rounded-2xl bg-slate-50 p-4">Lazismu: program donasi, QRIS, konfirmasi WhatsApp, dan transparansi penyaluran.</div>
                        <div class="rounded-2xl bg-slate-50 p-4">Site settings global: identitas, SEO, kontak, footer, dan warna dasar seluruh website.</div>
                    </div>
                </div>

                <div class="rounded-[30px] border border-white/60 bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_100%)] p-5 shadow-[0_12px_32px_rgba(15,23,42,0.08)]">
                    <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                        <x-public.ui.icon name="shield-check" class="h-4 w-4" />
                        <span>Prinsip Pengembangan</span>
                    </div>
                    <div class="mt-4 space-y-3 text-sm text-slate-700">
                        <div class="inline-flex items-center gap-2"><x-public.ui.icon name="badge-check" class="h-4 w-4 text-emerald-600" /> Transparansi program dan penyaluran</div>
                        <div class="inline-flex items-center gap-2"><x-public.ui.icon name="badge-check" class="h-4 w-4 text-emerald-600" /> UI ringan, mobile-first, dan mudah dipakai</div>
                        <div class="inline-flex items-center gap-2"><x-public.ui.icon name="badge-check" class="h-4 w-4 text-emerald-600" /> Backend modular dan terhubung ke admin panel</div>
                    </div>
                </div>
            </div>
        </div>

        @if ($footer['description'])
            <div class="rounded-[32px] border border-white/60 bg-white/90 p-6 text-sm leading-7 text-slate-600 shadow-[0_12px_32px_rgba(15,23,42,0.08)] md:p-8">
                {{ $footer['description'] }}
            </div>
        @endif
    </section>
</x-layouts.public>
