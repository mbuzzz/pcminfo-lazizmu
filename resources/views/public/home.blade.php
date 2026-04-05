<x-layouts.public :page-title="$siteSettings->siteName()" :page-description="$siteSettings->siteDescription()">
    @php
        $donation = $siteSettings->donation();
    @endphp

    <x-public.layout.hero-bento
        :stats="$stats"
        :featured-campaigns="$featuredCampaigns"
        :featured-posts="$featuredPosts"
        :upcoming-agendas="$upcomingAgendas"
        :latest-distributions="$latestDistributions"
    />

    <section class="mx-auto max-w-7xl space-y-14 px-4 py-4 md:px-6 md:space-y-20 md:py-8">
        <div class="space-y-6">
            <x-public.ui.section-header
                eyebrow="PCM Info"
                icon="newspaper"
                title="Berita & Artikel Terbaru"
                description="Informasi organisasi, kegiatan, dan gerakan yang sedang berjalan."
                :link="route('posts.index')"
                link-label="Lihat semua berita"
            />

            @if ($featuredPosts->isNotEmpty())
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($featuredPosts as $post)
                        <x-public.card.news-card :post="$post" />
                    @endforeach
                </div>
            @else
                <x-public.ui.empty-state title="Belum ada berita terbaru" description="Konten berita akan muncul di sini setelah dipublikasikan dari admin panel." />
            @endif
        </div>

        <div class="space-y-6">
            <x-public.ui.section-header
                eyebrow="Agenda"
                icon="calendar-range"
                title="Kegiatan Mendatang"
                description="Agenda dakwah, koordinasi, pendidikan, dan kegiatan sosial yang bisa Anda ikuti."
                :link="route('agendas.index')"
                link-label="Lihat semua agenda"
            />

            @if ($upcomingAgendas->isNotEmpty())
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($upcomingAgendas as $agenda)
                        <x-public.card.agenda-card :agenda="$agenda" />
                    @endforeach
                </div>
            @else
                <x-public.ui.empty-state title="Belum ada agenda terdekat" description="Agenda akan muncul di sini setelah dijadwalkan dan dipublikasikan." />
            @endif
        </div>

        <div class="space-y-6">
            <x-public.ui.section-header
                eyebrow="Organisasi"
                icon="building-2"
                title="Amal Usaha Unggulan"
                description="Sekolah, layanan, dan unit organisasi yang menjadi bagian dari ekosistem PCM Genteng."
                :link="route('institutions.index')"
                link-label="Jelajahi direktori"
            />

            @if ($featuredInstitutions->isNotEmpty())
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($featuredInstitutions as $institution)
                        <x-public.card.institution-card :institution="$institution" />
                    @endforeach
                </div>
            @else
                <x-public.ui.empty-state title="Belum ada amal usaha unggulan" description="Direktori amal usaha akan tampil di sini setelah data diaktifkan." />
            @endif
        </div>

        <div class="space-y-6">
            <x-public.ui.section-header
                eyebrow="Filantropi"
                icon="heart-handshake"
                title="Program Donasi Pilihan"
                description="Program berbasis dampak dengan progres yang transparan dan instruksi donasi yang mudah."
                :link="route('campaigns.index')"
                link-label="Lihat semua program"
            />

            @if ($featuredCampaigns->isNotEmpty())
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($featuredCampaigns as $campaign)
                        <x-public.card.campaign-card :campaign="$campaign" />
                    @endforeach
                </div>
            @else
                <x-public.ui.empty-state title="Belum ada program aktif" description="Program donasi dan filantropi akan tampil di sini saat sudah dipublikasikan." />
            @endif
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <x-public.ui.section-header
                    eyebrow="Donasi Cepat"
                    icon="qr-code"
                    title="QRIS & Konfirmasi yang Sederhana"
                    description="Pengunjung bisa langsung memindai QRIS global atau melanjutkan ke daftar program untuk donasi yang lebih spesifik."
                />

                <div class="rounded-[32px] border border-white/60 bg-white/95 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)] md:p-8">
                    <div class="grid gap-6 md:grid-cols-[0.8fr_1.2fr]">
                        @if ($donation['qris_image_url'])
                            <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white p-3">
                                <img src="{{ $donation['qris_image_url'] }}" alt="QRIS Donasi" class="w-full rounded-[24px] object-cover">
                            </div>
                        @endif

                        <div class="space-y-4">
                            <h3 class="text-2xl font-black tracking-tight text-slate-950">Salurkan dukungan dengan cepat, tetap transparan.</h3>
                            @if ($donation['instruction_text'])
                                <p class="text-sm leading-7 text-slate-600">{{ $donation['instruction_text'] }}</p>
                            @endif
                            @if ($donation['whatsapp_number'])
                                <div class="rounded-[24px] bg-slate-50 p-4 text-sm text-slate-700">
                                    Nomor konfirmasi: <span class="font-bold text-slate-950">{{ $donation['whatsapp_number'] }}</span>
                                </div>
                            @endif
                            <div class="flex flex-wrap gap-3">
                                <x-public.ui.button :href="route('campaigns.index')" variant="donation">
                                    {{ $donation['default_cta_text'] }}
                                </x-public.ui.button>
                                @if ($donation['whatsapp_number'])
                                    <x-public.ui.button
                                        :href="'https://wa.me/' . preg_replace('/[^0-9]/', '', $donation['whatsapp_number'])"
                                        variant="secondary"
                                        target="_blank"
                                        rel="noreferrer"
                                    >
                                        Konfirmasi WhatsApp
                                    </x-public.ui.button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <x-public.ui.section-header
                    eyebrow="Transparansi"
                    icon="hand-coins"
                    title="Laporan Penyaluran Terbaru"
                    description="Penyaluran program yang sudah terealisasi dan tercatat secara terbuka."
                    :link="route('distributions.index')"
                    link-label="Lihat seluruh laporan"
                />

                @if ($latestDistributions->isNotEmpty())
                    <div class="grid gap-4">
                        @foreach ($latestDistributions as $distribution)
                            <x-public.card.distribution-card :distribution="$distribution" />
                        @endforeach
                    </div>
                @else
                    <x-public.ui.empty-state title="Belum ada laporan penyaluran" description="Laporan penyaluran akan tampil di sini setelah program berjalan." />
                @endif
            </div>
        </div>

        <div class="grid gap-6">
            <div class="space-y-6">
                <x-public.ui.section-header
                    eyebrow="E-Struktur"
                    icon="users"
                    title="Pimpinan Terkini"
                    description="Ringkasan struktur pimpinan yang aktif saat ini."
                    :link="route('leaders.index')"
                    link-label="Lihat struktur lengkap"
                />

                @if ($leaders->isNotEmpty())
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($leaders as $leader)
                            <x-public.card.leader-card :leader="$leader" />
                        @endforeach
                    </div>
                @else
                    <x-public.ui.empty-state title="Belum ada struktur pimpinan" description="Data pimpinan aktif akan tampil setelah dilengkapi dari panel admin." />
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
