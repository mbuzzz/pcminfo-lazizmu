@php
    $items = app(\App\Support\PublicNavigation::class)->mobileBottomItems();
@endphp

<nav class="fixed inset-x-0 bottom-0 z-40 px-3 pb-[calc(env(safe-area-inset-bottom,0px)_+_0.75rem)] pt-2 lg:hidden">
    <div class="mx-auto grid max-w-lg grid-cols-5 gap-2 rounded-[28px] border border-white/70 bg-white/92 p-2 shadow-[0_18px_50px_rgba(15,23,42,0.16)] backdrop-blur-xl">
        @foreach ($items as $item)
            @php($targetUrl = route($item['route'], $item['query'] ?? []))
            @php($isActive = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])))
            <a
                href="{{ $targetUrl }}"
                aria-label="{{ $item['label'] }}"
                class="{{ ($item['featured'] ?? false) ? 'bg-[linear-gradient(135deg,#E8242A_0%,#F1B12D_100%)] text-white shadow-[0_16px_36px_rgba(232,36,42,0.28)]' : ($isActive ? 'bg-slate-950 text-white shadow-sm' : 'text-slate-500') }} flex min-h-14 items-center justify-center rounded-[22px] px-2 py-2.5 transition"
                wire:navigate
            >
                <x-public.ui.icon :name="$item['icon']" class="h-5 w-5" />
                <span class="sr-only">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
