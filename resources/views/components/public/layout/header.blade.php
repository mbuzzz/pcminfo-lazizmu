@php
    $identity = $siteSettings->identity();
    $donation = $siteSettings->donation();
    $navItems = app(\App\Support\PublicNavigation::class)->primaryItems();
@endphp

<header x-data="{ searchOpen: false }" class="sticky top-0 z-40 px-4 pt-3 md:px-6 md:pt-4">
    <div class="mx-auto max-w-7xl rounded-[30px] border border-white/65 bg-white/82 shadow-[0_18px_50px_rgba(15,23,42,0.10)] backdrop-blur-xl">
    <div class="flex items-center justify-between gap-4 px-4 py-3 md:px-5">
        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3" wire:navigate>
            @if ($identity['logo_url'])
                <img src="{{ $identity['logo_url'] }}" alt="Logo {{ $identity['name'] }}" class="h-12 w-12 rounded-2xl object-cover shadow-md ring-1 ring-slate-200/70">
            @else
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl text-sm font-bold text-white shadow-md" style="background: linear-gradient(135deg, var(--site-primary) 0%, var(--site-secondary) 100%);">
                    {{ \Illuminate\Support\Str::of($identity['name'])->substr(0, 2)->upper() }}
                </div>
            @endif

            <div class="min-w-0">
                <div class="truncate text-[11px] font-bold uppercase tracking-[0.24em] text-slate-500">Portal Digital</div>
                <div class="truncate text-base font-black tracking-tight text-slate-950 md:text-lg">{{ $identity['name'] }}</div>
            </div>
        </a>

        <div class="hidden items-center gap-3 lg:flex">
            <nav class="flex items-center gap-1 rounded-full border border-slate-200/80 bg-slate-50/90 p-1">
                @foreach ($navItems as $item)
                    @php($isActive = request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route'])))
                    <a
                        href="{{ route($item['route']) }}"
                        class="{{ $isActive ? 'bg-slate-950 text-white shadow-sm' : 'text-slate-700 hover:bg-white hover:text-slate-950' }} inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition"
                        wire:navigate
                    >
                        <x-public.ui.icon :name="$item['icon']" class="h-4 w-4" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="hidden items-center gap-2 md:flex">
            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 transition hover:border-slate-300 hover:text-slate-950"
                x-on:click="searchOpen = ! searchOpen"
                :aria-expanded="searchOpen.toString()"
            >
                <x-public.ui.icon name="search" class="h-5 w-5" />
            </button>

            <x-public.ui.button :href="route('campaigns.index')" variant="donation" icon="badge-cent" class="inline-flex" wire:navigate>
                {{ $donation['default_cta_text'] }}
            </x-public.ui.button>
        </div>
    </div>
    </div>

    <div x-show="searchOpen" x-cloak x-transition class="mx-auto mt-3 hidden max-w-7xl rounded-[28px] border border-white/65 bg-white/88 px-4 py-4 shadow-[0_16px_40px_rgba(15,23,42,0.08)] backdrop-blur-xl md:block">
        <div class="mx-auto max-w-7xl">
            <livewire:public.search.header-quick-search :key="'desktop-header-quick-search'" />
        </div>
    </div>

</header>
