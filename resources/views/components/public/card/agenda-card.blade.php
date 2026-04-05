@props(['agenda'])

<article class="group rounded-[28px] border border-white/60 bg-white/90 p-4 shadow-[0_12px_32px_rgba(15,23,42,0.08)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_20px_44px_rgba(15,23,42,0.12)]">
    <div class="space-y-4">
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    <span>{{ optional($agenda->type)->getLabel() ?? 'Agenda' }}</span>
                    @if ($agenda->category)
                        <a href="{{ route('agendas.categories.show', $agenda->category) }}" class="relative z-10 inline-flex items-center gap-1.5 transition hover:text-slate-700">
                            <x-public.ui.icon name="bookmark" class="h-3.5 w-3.5" />
                            <span>{{ $agenda->category->name }}</span>
                        </a>
                    @endif
                </div>
                <a href="{{ route('agendas.show', $agenda) }}" class="block">
                    <h3 class="line-clamp-2 text-lg font-bold tracking-tight text-slate-950 transition group-hover:text-[var(--site-primary)]">{{ $agenda->title }}</h3>
                </a>
            </div>
            <div class="rounded-2xl bg-slate-100 px-3 py-2 text-center">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $agenda->start_at?->translatedFormat('M') }}</div>
                <div class="text-xl font-bold text-slate-950">{{ $agenda->start_at?->translatedFormat('d') }}</div>
            </div>
        </div>

        <div class="space-y-2 text-sm text-slate-600">
            <div class="inline-flex items-center gap-2"><x-public.ui.icon name="clock-3" class="h-4 w-4" /> {{ $agenda->start_at?->translatedFormat('l, d M Y · H:i') }}</div>
            @if ($agenda->location_name)
                <div class="inline-flex items-center gap-2"><x-public.ui.icon name="map-pin" class="h-4 w-4" /> {{ $agenda->location_name }}</div>
            @endif
            @if ($agenda->institution)
                <div class="inline-flex items-center gap-2 font-medium text-slate-700"><x-public.ui.icon name="building-2" class="h-4 w-4" /> {{ $agenda->institution->name }}</div>
            @endif
        </div>

        <a href="{{ route('agendas.show', $agenda) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--site-primary)]">
            <span>Lihat detail agenda</span>
            <x-public.ui.icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>
</article>
