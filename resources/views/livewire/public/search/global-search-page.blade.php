@php
    $gridClasses = [
        'posts' => 'grid gap-4 md:grid-cols-2 xl:grid-cols-3',
        'agendas' => 'grid gap-4 md:grid-cols-2 xl:grid-cols-3',
        'campaigns' => 'grid gap-4 md:grid-cols-2 xl:grid-cols-3',
        'institutions' => 'grid gap-4 md:grid-cols-2 xl:grid-cols-3',
    ];
@endphp

<div class="space-y-8">
    <div class="rounded-[32px] border border-white/60 bg-white/90 p-5 shadow-[0_16px_42px_rgba(15,23,42,0.10)] md:p-7">
        <div class="flex flex-col gap-5">
            <div class="space-y-3">
                <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                    <x-public.ui.icon name="search" class="h-3.5 w-3.5" />
                    Pencarian Global
                </div>
                <div class="space-y-2">
                    <h1 class="text-3xl font-black tracking-tight text-slate-950 md:text-5xl">Cari berita, agenda, program, dan amal usaha</h1>
                    <p class="max-w-3xl text-sm leading-7 text-slate-600 md:text-base">
                        Semua hasil ditarik langsung dari data publik yang sudah dipublikasikan pada portal. Tidak ada konten yang dihardcode.
                    </p>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center">
                <label class="relative block">
                    <x-public.ui.icon name="search" class="pointer-events-none absolute left-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari topik, program, kegiatan, atau nama amal usaha..."
                        class="w-full rounded-[26px] border border-slate-200 bg-slate-50 py-4 pl-14 pr-5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-300 focus:bg-white focus:outline-none"
                    >
                </label>

                <div class="flex flex-wrap gap-2">
                    @foreach ($tabs as $tabValue => $tabLabel)
                        <button
                            type="button"
                            wire:click="$set('type', '{{ $tabValue }}')"
                            class="{{ $type === $tabValue ? 'bg-slate-950 text-white shadow-sm' : 'bg-white text-slate-700 hover:bg-slate-100' }} inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition"
                        >
                            {{ $tabLabel }}
                            <span class="ml-2 rounded-full {{ $type === $tabValue ? 'bg-white/15 text-white' : 'bg-slate-100 text-slate-500' }} px-2 py-0.5 text-[11px] font-bold">
                                {{ number_format($counts[$tabValue] ?? 0) }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            @if ($search !== '' || $type !== 'all')
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-[24px] bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    <div class="flex items-center gap-2">
                        <x-public.ui.icon name="sparkles" class="h-4 w-4 text-slate-400" />
                        <span>
                            @if ($search !== '')
                                Menampilkan hasil untuk <span class="font-bold text-slate-950">"{{ $search }}"</span>
                            @else
                                Menampilkan hasil untuk kategori <span class="font-bold text-slate-950">{{ $tabs[$type] ?? 'Semua' }}</span>
                            @endif
                        </span>
                    </div>

                    <button type="button" wire:click="clearFilters" class="inline-flex items-center gap-2 font-semibold text-slate-700 transition hover:text-slate-950">
                        <x-public.ui.icon name="rotate-ccw" class="h-4 w-4" />
                        Reset pencarian
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if ($type === 'all')
        <div class="space-y-8">
            @foreach ($tabs as $section => $label)
                @continue($section === 'all')

                <section class="space-y-4">
                    <x-public.ui.section-header
                        :eyebrow="$label"
                        :title="'Hasil ' . $label"
                        :description="'Potongan hasil terbaru yang relevan dari modul ' . strtolower($label) . '.'"
                    />

                    @if ($sections[$section]->isNotEmpty())
                        <div class="{{ $gridClasses[$section] }}">
                            @foreach ($sections[$section] as $item)
                                @if ($section === 'posts')
                                    <x-public.card.news-card :post="$item" wire:key="{{ $section }}-{{ $item->getKey() }}" />
                                @elseif ($section === 'agendas')
                                    <x-public.card.agenda-card :agenda="$item" wire:key="{{ $section }}-{{ $item->getKey() }}" />
                                @elseif ($section === 'campaigns')
                                    <x-public.card.campaign-card :campaign="$item" wire:key="{{ $section }}-{{ $item->getKey() }}" />
                                @elseif ($section === 'institutions')
                                    <x-public.card.institution-card :institution="$item" wire:key="{{ $section }}-{{ $item->getKey() }}" />
                                @endif
                            @endforeach
                        </div>
                    @else
                        <x-public.ui.empty-state
                            icon="search-x"
                            :title="'Belum ada hasil pada ' . $label"
                            description="Coba gunakan kata kunci lain atau pilih tab pencarian yang lebih spesifik."
                        />
                    @endif
                </section>
            @endforeach
        </div>
    @elseif ($results)
        <section class="space-y-4">
            <x-public.ui.section-header
                :eyebrow="$tabs[$type] ?? 'Hasil'"
                :title="'Semua hasil ' . ($tabs[$type] ?? 'Pencarian')"
                :description="'Daftar lengkap hasil yang cocok dengan kata kunci dan filter yang sedang aktif.'"
            />

            @if ($results->count() > 0)
                <div class="{{ $gridClasses[$type] }}">
                    @foreach ($results as $item)
                        @if ($type === 'posts')
                            <x-public.card.news-card :post="$item" wire:key="{{ $type }}-{{ $item->getKey() }}" />
                        @elseif ($type === 'agendas')
                            <x-public.card.agenda-card :agenda="$item" wire:key="{{ $type }}-{{ $item->getKey() }}" />
                        @elseif ($type === 'campaigns')
                            <x-public.card.campaign-card :campaign="$item" wire:key="{{ $type }}-{{ $item->getKey() }}" />
                        @elseif ($type === 'institutions')
                            <x-public.card.institution-card :institution="$item" wire:key="{{ $type }}-{{ $item->getKey() }}" />
                        @endif
                    @endforeach
                </div>

                <div>
                    {{ $results->links() }}
                </div>
            @else
                <x-public.ui.empty-state
                    icon="search-x"
                    title="Tidak ada hasil yang cocok"
                    description="Coba kata kunci yang lebih umum, atau ubah tab pencarian ke modul lain."
                />
            @endif
        </section>
    @endif
</div>
