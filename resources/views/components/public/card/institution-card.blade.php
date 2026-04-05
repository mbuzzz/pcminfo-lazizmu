@props(['institution'])

<article class="group overflow-hidden rounded-[28px] border border-white/60 bg-white/90 shadow-[0_12px_32px_rgba(15,23,42,0.08)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_20px_44px_rgba(15,23,42,0.12)]">
    <a href="{{ route('institutions.show', $institution) }}" class="block">
        <div class="aspect-[16/10] overflow-hidden bg-slate-100">
            @if ($institution->cover_image_url)
                <img src="{{ $institution->cover_image_url }}" alt="{{ $institution->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy">
            @elseif ($institution->logo_url)
                <div class="flex h-full items-center justify-center bg-[linear-gradient(135deg,#eef2ff,#ecfeff)]">
                    <img src="{{ $institution->logo_url }}" alt="{{ $institution->name }}" class="h-24 w-24 rounded-3xl object-cover shadow-lg">
                </div>
            @else
                <div class="flex h-full items-center justify-center bg-[linear-gradient(135deg,#eef2ff,#f8fafc)] text-sm font-semibold text-slate-500">Amal Usaha</div>
            @endif
        </div>
        <div class="space-y-3 p-5">
            <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ optional($institution->type)->getLabel() ?? 'Organisasi' }}</div>
            <h3 class="text-lg font-bold tracking-tight text-slate-950">{{ $institution->name }}</h3>
            @if ($institution->tagline)
                <p class="line-clamp-2 text-sm leading-6 text-slate-600">{{ $institution->tagline }}</p>
            @endif
            <div class="flex items-center justify-between text-sm text-slate-500">
                <span class="inline-flex items-center gap-1.5"><x-public.ui.icon name="map-pin" class="h-4 w-4" /> {{ $institution->city ?: 'Genteng' }}</span>
                <span class="font-medium text-slate-700">{{ ucfirst($institution->status) }}</span>
            </div>
        </div>
    </a>
</article>
