@props(['leader'])

<article class="rounded-[28px] border border-white/60 bg-white/90 p-5 text-center shadow-[0_12px_32px_rgba(15,23,42,0.08)]">
    <div class="mx-auto mb-4 h-24 w-24 overflow-hidden rounded-full bg-slate-100 ring-4 ring-white shadow-lg">
        @if ($leader->photo_url)
            <img src="{{ $leader->photo_url }}" alt="{{ $leader->name }}" class="h-full w-full object-cover" loading="lazy">
        @else
            <div class="flex h-full items-center justify-center text-2xl font-bold text-slate-400">{{ \Illuminate\Support\Str::of($leader->name)->substr(0, 1)->upper() }}</div>
        @endif
    </div>
    <h3 class="text-base font-bold text-slate-950">{{ $leader->name }}</h3>
    <p class="mt-1 inline-flex items-center gap-2 text-sm font-medium text-slate-600"><x-public.ui.icon name="badge-check" class="h-4 w-4" /> {{ $leader->position }}</p>
    <p class="mt-2 inline-flex items-center gap-2 text-xs uppercase tracking-[0.18em] text-slate-500"><x-public.ui.icon name="calendar-range" class="h-3.5 w-3.5" /> {{ $leader->period }}</p>
</article>
