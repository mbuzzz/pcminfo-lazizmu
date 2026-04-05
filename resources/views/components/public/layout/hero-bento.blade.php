@props([
    'stats' => [],
    'featuredCampaigns',
    'featuredPosts',
    'upcomingAgendas',
    'latestDistributions',
])

@php
    $identity = $siteSettings->identity();
    $donation = $siteSettings->donation();
    $homepageFeature = $siteSettings->homepageFeature();
    $heroCampaign = $featuredCampaigns->first();
    $latestDistribution = $latestDistributions->first();
    $spotlightPost = $featuredPosts->skip(1)->first();
    $supportingPost = $featuredPosts->skip(2)->first();
    $nextAgenda = $upcomingAgendas->first();
@endphp

<section class="mx-auto max-w-7xl px-4 py-8 md:px-6 md:py-10 space-y-4 lg:space-y-6">
    
    <!-- === HERO TOP SECTION === -->
    <div class="grid gap-4 lg:gap-6 lg:grid-cols-[1.6fr_1fr] xl:grid-cols-[2fr_1fr]">
        <div class="w-full h-full">
            <x-public.hero.article-slider :posts="$featuredPosts" />
        </div>
        
        <div class="flex flex-col gap-4 lg:gap-6">
            <div class="relative overflow-hidden rounded-[32px] border border-white/60 bg-[linear-gradient(180deg,#fff7ed_0%,#ffffff_100%)] p-6 shadow-[0_18px_44px_rgba(232,36,42,0.12)]">
                <div class="absolute -right-8 -top-10 h-28 w-28 rounded-full bg-orange-200/50 blur-2xl"></div>
                <div class="absolute bottom-0 right-0 h-24 w-24 rounded-tl-[32px] border-l border-t border-orange-100/70 bg-white/50"></div>

                <div class="relative inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-orange-500 shadow-sm">
                    <x-public.ui.icon name="heart-handshake" class="h-3.5 w-3.5" />
                    Donasi Cepat
                </div>

                <div class="relative mt-4 space-y-3">
                    <h2 class="text-2xl font-black tracking-tight text-slate-950">{{ $donation['donasi_cepat_title'] ?: 'Dukung gerakan yang lebih transparan dan mudah dijangkau.' }}</h2>
                    <p class="text-sm leading-7 text-slate-600">{{ $donation['donasi_cepat_description'] ?: 'Gunakan QRIS global, lanjutkan ke program spesifik, atau konfirmasi langsung melalui WhatsApp organisasi.' }}</p>
                </div>

                <div class="relative mt-5 flex flex-wrap gap-3">
                    <x-public.ui.button :href="route('campaigns.index')" variant="donation" icon="qr-code">
                        {{ $donation['default_cta_text'] }}
                    </x-public.ui.button>
                    @if ($donation['whatsapp_number'])
                        <x-public.ui.button :href="'https://wa.me/' . preg_replace('/[^0-9]/', '', $donation['whatsapp_number'])" variant="secondary" icon="message-circle" target="_blank" rel="noreferrer">
                            WhatsApp
                        </x-public.ui.button>
                    @endif
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[32px] border border-white/60 bg-slate-950 p-6 text-white shadow-[0_24px_60px_rgba(15,23,42,0.24)]">
                <div class="absolute left-6 top-6 h-24 w-24 rounded-full bg-white/8 blur-2xl"></div>
                <div class="absolute bottom-0 right-0 h-36 w-36 rounded-full bg-[color-mix(in_srgb,var(--site-accent)_30%,transparent)] blur-3xl"></div>

                <div class="relative inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-white/80">
                    <x-public.ui.icon name="quote" class="h-3.5 w-3.5" />
                    {{ $homepageFeature['badge'] ?: 'Nilai Gerakan' }}
                </div>
                @if ($homepageFeature['title'])
                    <p class="relative mt-4 text-xl font-black leading-tight tracking-tight md:text-2xl">{{ $homepageFeature['title'] }}</p>
                @endif
                @if ($homepageFeature['description'])
                    <p class="relative mt-3 text-sm leading-7 text-white/70">{{ $homepageFeature['description'] }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- === BENTO MASONRY SECTION === -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6 items-start">
        
        <!-- COLUMN A -->
        <div class="flex flex-col gap-4 lg:gap-6 w-full">
            <div class="relative overflow-hidden rounded-[32px] border border-white/60 bg-white/90 p-5 xl:p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)]">
                <div class="absolute -left-8 top-12 h-28 w-28 rounded-full bg-[color-mix(in_srgb,var(--site-primary)_12%,white)] blur-2xl"></div>
                <div class="relative inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500">
                    <x-public.ui.icon name="sparkles" class="h-3.5 w-3.5" />
                    {{ $identity['ekosistem_gerakan_badge'] ?: 'Ekosistem Gerakan' }}
                </div>
                <div class="relative mt-4 space-y-3">
                    <h2 class="text-2xl font-black tracking-tight text-slate-950 md:text-3xl">{{ $identity['ekosistem_gerakan_title'] ?: $identity['name'] }}</h2>
                    @if ($identity['ekosistem_gerakan_description'])
                        <p class="text-sm leading-7 text-slate-600">{{ $identity['ekosistem_gerakan_description'] }}</p>
                    @elseif ($identity['tagline'])
                        <p class="text-base font-medium text-slate-700">{{ $identity['tagline'] }}</p>
                    @endif
                </div>
            </div>

            <div class="relative overflow-hidden rounded-[32px] border border-white/60 bg-white/95 p-5 xl:p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)]">
                <div class="absolute -right-8 -top-10 h-28 w-28 rounded-full bg-[color-mix(in_srgb,var(--site-primary)_14%,white)] blur-2xl"></div>
                <div class="mb-4 flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Program Unggulan</div>
                        <div class="text-lg font-bold text-slate-950">{{ $heroCampaign?->title ?? 'Program akan tampil di sini' }}</div>
                    </div>
                    @if ($heroCampaign)
                        <div class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600 whitespace-nowrap">{{ $heroCampaign->type->getLabel() }}</div>
                    @endif
                </div>
                @if ($heroCampaign)
                    @if ($heroCampaign->short_description)
                        <p class="mb-4 text-sm leading-6 text-slate-600">{{ $heroCampaign->short_description }}</p>
                    @endif
                    <x-public.ui.progress-bar :percentage="$heroCampaign->progress_percentage" color="#E8242A" />
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-slate-50 p-3">
                            <div class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500">Terkumpul</div>
                            <div class="mt-2 text-sm font-bold text-slate-950">{{ $heroCampaign->goal_type === 'nominal' ? $heroCampaign->getFormattedCollectedAmount() : number_format($heroCampaign->collected_unit) . ' ' . $heroCampaign->unit_label }}</div>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-3">
                            <div class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500">Donatur</div>
                            <div class="mt-2 text-sm font-bold text-slate-950">{{ number_format($heroCampaign->donor_count) }}</div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <x-public.ui.button :href="route('campaigns.show', $heroCampaign)" variant="primary" icon="arrow-right" icon-position="trailing" class="w-full justify-center">
                            Lihat Detail Program
                        </x-public.ui.button>
                    </div>
                @endif
            </div>
        </div>

        <!-- COLUMN B -->
        <div class="flex flex-col gap-4 lg:gap-6 w-full">
            @if ($spotlightPost)
                <a href="{{ route('posts.show', $spotlightPost) }}" class="group relative overflow-hidden rounded-[32px] border border-white/60 bg-slate-950 shadow-[0_20px_50px_rgba(15,23,42,0.16)] flex flex-col min-h-[340px]">
                    @if ($spotlightPost->featured_image_url)
                        <img src="{{ $spotlightPost->featured_image_url }}" alt="{{ $spotlightPost->title }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                    @endif
                    <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(15,23,42,0.06)_0%,rgba(15,23,42,0.78)_100%)]"></div>
                    <div class="relative z-10 flex min-h-[250px] flex-col justify-end p-5 text-white flex-1">
                        <div class="inline-flex w-max items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/85 backdrop-blur">
                            <x-public.ui.icon name="book-open-text" class="h-3.5 w-3.5" />
                            Sorotan
                        </div>
                        <h3 class="mt-3 line-clamp-3 text-xl font-black tracking-tight">{{ $spotlightPost->title }}</h3>
                        @if ($spotlightPost->excerpt)
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-white/78">{{ $spotlightPost->excerpt }}</p>
                        @endif
                    </div>
                </a>
            @endif

            <div class="relative overflow-hidden rounded-[32px] border border-white/60 bg-[linear-gradient(135deg,rgba(44,54,139,0.96)_0%,rgba(16,129,111,0.92)_100%)] p-5 xl:p-6 text-white shadow-[0_20px_50px_rgba(44,54,139,0.20)]">
                <div class="absolute -right-8 top-0 h-28 w-28 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute left-8 top-8 h-16 w-16 rounded-full border border-white/10"></div>
                <div class="relative text-xs font-semibold uppercase tracking-[0.18em] text-white/70">Transparansi</div>
                <div class="relative mt-3 text-xl font-black tracking-tight xl:text-2xl">Data penyaluran yang lebih mudah dibaca publik.</div>
                <p class="relative mt-3 text-sm leading-7 text-white/75">{{ $latestDistribution?->title ?? 'Laporan terbaru akan tampil di sini setelah penyaluran dicatat.' }}</p>
                <div class="relative mt-5">
                    <x-public.ui.button :href="route('distributions.index')" variant="secondary" icon="hand-coins">
                        Lihat Penyaluran
                    </x-public.ui.button>
                </div>
            </div>

            <div class="rounded-[32px] border border-white/60 bg-white/95 p-5 xl:p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)]">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Akses Cepat</div>
                <div class="mt-4 grid gap-3">
                    <a href="{{ route('posts.index') }}" class="flex items-center justify-between rounded-[22px] bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-100">
                        <span class="inline-flex items-center gap-2"><x-public.ui.icon name="newspaper" class="h-4 w-4" /> Berita Terbaru</span>
                        <x-public.ui.icon name="arrow-right" class="h-4 w-4" />
                    </a>
                    <a href="{{ route('institutions.index') }}" class="flex items-center justify-between rounded-[22px] bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-100">
                        <span class="inline-flex items-center gap-2"><x-public.ui.icon name="building-2" class="h-4 w-4" /> Amal Usaha</span>
                        <x-public.ui.icon name="arrow-right" class="h-4 w-4" />
                    </a>
                    <a href="{{ route('leaders.index') }}" class="flex items-center justify-between rounded-[22px] bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-100">
                        <span class="inline-flex items-center gap-2"><x-public.ui.icon name="users" class="h-4 w-4" /> Pimpinan</span>
                        <x-public.ui.icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>
            </div>
        </div>

        <!-- COLUMN C -->
        <div class="flex flex-col gap-4 lg:gap-6 w-full">
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-[24px] border border-white/50 bg-white/90 p-3 shadow-[0_12px_32px_rgba(15,23,42,0.08)]">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-500 mb-1">Artikel</div>
                    <div class="text-2xl font-black text-slate-950">{{ number_format($stats['posts'] ?? 0) }}</div>
                </div>
                <div class="rounded-[24px] border border-white/50 bg-white/90 p-3 shadow-[0_12px_32px_rgba(15,23,42,0.08)]">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-500 mb-1">Agenda</div>
                    <div class="text-2xl font-black text-slate-950">{{ number_format($stats['agendas'] ?? 0) }}</div>
                </div>
                <div class="rounded-[24px] border border-white/50 bg-white/90 p-3 shadow-[0_12px_32px_rgba(15,23,42,0.08)]">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-500 mb-1">Inst.</div>
                    <div class="text-2xl font-black text-slate-950">{{ number_format($stats['institutions'] ?? 0) }}</div>
                </div>
                <div class="rounded-[24px] border border-white/50 bg-white/90 p-3 shadow-[0_12px_32px_rgba(15,23,42,0.08)]">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-500 mb-1">Prog.</div>
                    <div class="text-2xl font-black text-slate-950">{{ number_format($stats['campaigns'] ?? 0) }}</div>
                </div>
            </div>

            @if ($supportingPost)
                <a href="{{ route('posts.show', $supportingPost) }}" class="group relative overflow-hidden rounded-[32px] border border-white/60 bg-slate-950 shadow-[0_14px_36px_rgba(15,23,42,0.08)] flex flex-col min-h-[260px]">
                    @if ($supportingPost->featured_image_url)
                        <img src="{{ $supportingPost->featured_image_url }}" alt="{{ $supportingPost->title }}" class="absolute inset-0 h-full w-full object-cover opacity-85 transition duration-500 group-hover:scale-[1.04]">
                    @endif
                    <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(15,23,42,0)_0%,rgba(15,23,42,0.85)_100%)]"></div>
                    <div class="relative z-10 flex flex-col justify-end p-5 flex-1 text-white">
                        <div class="mb-3 inline-flex w-max items-center gap-2 rounded-full bg-white/20 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-white/90 backdrop-blur">
                            <x-public.ui.icon name="newspaper" class="h-3 w-3" />
                            Update Cepat
                        </div>
                        <h3 class="line-clamp-3 text-lg font-black tracking-tight">{{ $supportingPost->title }}</h3>
                    </div>
                </a>
            @endif

            @if ($nextAgenda)
                <a href="{{ route('agendas.show', $nextAgenda) }}" class="group rounded-[32px] border border-white/60 bg-white/90 p-5 xl:p-6 shadow-[0_14px_36px_rgba(15,23,42,0.08)] flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="inline-flex w-max items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                                <x-public.ui.icon name="calendar-clock" class="h-3.5 w-3.5" />
                                Terdekat
                            </div>
                            <h3 class="mt-4 line-clamp-2 text-xl font-black tracking-tight text-slate-950">{{ $nextAgenda->title }}</h3>
                        </div>
                        <div class="rounded-[24px] bg-slate-950 px-3 py-2 text-center text-white shadow-sm flex-shrink-0">
                            <div class="text-[10px] font-semibold uppercase tracking-[0.18em] text-white/60">{{ $nextAgenda->start_at?->translatedFormat('M') }}</div>
                            <div class="text-xl font-black">{{ $nextAgenda->start_at?->translatedFormat('d') }}</div>
                        </div>
                    </div>
                </a>
            @endif

            <div class="rounded-[32px] border border-white/60 bg-white/95 p-5 xl:p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)]">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Agenda</div>
                        <div class="text-lg font-bold text-slate-950">Selanjutnya</div>
                    </div>
                    <a href="{{ route('agendas.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-700 whitespace-nowrap">Semua <x-public.ui.icon name="arrow-right" class="h-4 w-4" /></a>
                </div>
                @if ($upcomingAgendas->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($upcomingAgendas->take(2) as $agenda)
                            <a href="{{ route('agendas.show', $agenda) }}" class="flex items-start justify-between gap-3 rounded-[24px] bg-slate-50 p-3 transition hover:bg-slate-100">
                                <div class="space-y-1">
                                    <div class="text-sm font-semibold text-slate-950">{{ $agenda->title }}</div>
                                    <div class="inline-flex items-center gap-2 text-[11px] text-slate-500"><x-public.ui.icon name="clock-3" class="h-3 w-3" /> {{ $agenda->start_at?->translatedFormat('H:i') }}</div>
                                </div>
                                <div class="rounded-[18px] bg-white px-2 py-1 text-center shadow-sm flex-shrink-0">
                                    <div class="text-[9px] font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $agenda->start_at?->translatedFormat('M') }}</div>
                                    <div class="text-base font-bold text-slate-950">{{ $agenda->start_at?->translatedFormat('d') }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>
</section>
