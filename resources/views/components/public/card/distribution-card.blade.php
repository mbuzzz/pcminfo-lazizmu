@props(['distribution'])

<article class="rounded-[28px] border border-white/60 bg-white/90 p-5 shadow-[0_12px_32px_rgba(15,23,42,0.08)]">
    <div class="space-y-3">
        <div class="flex items-center justify-between gap-4">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $distribution->distribution_date?->translatedFormat('d M Y') }}</div>
            <div class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ optional($distribution->status)->getLabel() ?? ucfirst((string) $distribution->status) }}</div>
        </div>
        <h3 class="text-lg font-bold tracking-tight text-slate-950">{{ $distribution->title }}</h3>
        @if ($distribution->description)
            <p class="line-clamp-3 text-sm leading-6 text-slate-600">{{ $distribution->description }}</p>
        @endif
        <div class="flex flex-wrap gap-2 text-sm text-slate-600">
            @if ($distribution->campaign)
                <span class="inline-flex items-center gap-1.5"><x-public.ui.icon name="heart-handshake" class="h-4 w-4" /> {{ $distribution->campaign->title }}</span>
            @endif
            @if ($distribution->location)
                <span class="inline-flex items-center gap-1.5"><x-public.ui.icon name="map-pin" class="h-4 w-4" /> {{ $distribution->location }}</span>
            @endif
        </div>
    </div>
</article>
