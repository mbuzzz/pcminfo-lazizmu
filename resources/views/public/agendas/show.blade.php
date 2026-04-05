<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription" :page-image="$pageImage">
    <section class="mx-auto max-w-5xl px-4 py-8 md:px-6 md:py-12">
        <div class="mb-6">
            <a href="{{ route('agendas.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:text-slate-950">
                <x-public.ui.icon name="arrow-left" class="h-4 w-4" />
                <span>Kembali ke agenda</span>
            </a>
        </div>
        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6 rounded-[32px] border border-white/60 bg-white/90 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)] md:p-8">
                <div class="space-y-3">
                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ optional($agenda->type)->getLabel() ?? 'Agenda' }}</div>
                    <h1 class="text-3xl font-black tracking-tight text-slate-950 md:text-5xl">{{ $agenda->title }}</h1>
                </div>

                <div class="prose prose-slate max-w-none">
                    {!! nl2br(e($agenda->description)) !!}
                </div>
            </div>

            <aside class="space-y-4 rounded-[32px] border border-white/60 bg-white/90 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)]">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Waktu</div>
                    <div class="mt-2 inline-flex items-center gap-2 text-base font-semibold text-slate-950"><x-public.ui.icon name="clock-3" class="h-4 w-4" /> {{ $agenda->start_at?->translatedFormat('l, d F Y · H:i') }}</div>
                </div>
                @if ($agenda->location_name)
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Lokasi</div>
                        <div class="mt-2 inline-flex items-center gap-2 text-base font-semibold text-slate-950"><x-public.ui.icon name="map-pin" class="h-4 w-4" /> {{ $agenda->location_name }}</div>
                        @if ($agenda->location_address)
                            <div class="mt-1 text-sm text-slate-600">{{ $agenda->location_address }}</div>
                        @endif
                    </div>
                @endif
                @if ($agenda->institution)
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Penyelenggara</div>
                        <div class="mt-2 inline-flex items-center gap-2 text-base font-semibold text-slate-950"><x-public.ui.icon name="building-2" class="h-4 w-4" /> {{ $agenda->institution->name }}</div>
                    </div>
                @endif
            </aside>
        </div>
    </section>
</x-layouts.public>
