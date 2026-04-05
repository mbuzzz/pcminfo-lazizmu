<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription" :page-image="$pageImage">
    <section class="mx-auto max-w-6xl space-y-8 px-4 py-8 md:px-6 md:py-12">
        <div>
            <a href="{{ route('institutions.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:text-slate-950">
                <x-public.ui.icon name="arrow-left" class="h-4 w-4" />
                <span>Kembali ke direktori</span>
            </a>
        </div>
        <div class="overflow-hidden rounded-[32px] border border-white/60 bg-white/90 shadow-[0_16px_42px_rgba(15,23,42,0.10)]">
            @if ($institution->cover_image_url)
                <div class="aspect-[16/6] overflow-hidden">
                    <img src="{{ $institution->cover_image_url }}" alt="{{ $institution->name }}" class="h-full w-full object-cover">
                </div>
            @endif
            <div class="grid gap-6 p-6 md:grid-cols-[0.8fr_1.2fr] md:p-8">
                <div class="space-y-4">
                    @if ($institution->logo_url)
                        <img src="{{ $institution->logo_url }}" alt="{{ $institution->name }}" class="h-28 w-28 rounded-[28px] object-cover shadow-lg">
                    @endif
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ optional($institution->type)->getLabel() ?? 'Amal Usaha' }}</div>
                        <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ $institution->name }}</h1>
                        @if ($institution->tagline)
                            <p class="mt-3 text-base leading-7 text-slate-600">{{ $institution->tagline }}</p>
                        @endif
                    </div>
                </div>
                <div class="space-y-4">
                    @if ($institution->description)
                        <p class="text-sm leading-7 text-slate-700">{{ $institution->description }}</p>
                    @endif
                    <div class="grid gap-3 sm:grid-cols-2">
                        @if ($institution->phone)<div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700"><span class="inline-flex items-center gap-2"><x-public.ui.icon name="phone" class="h-4 w-4" /> Telepon: {{ $institution->phone }}</span></div>@endif
                        @if ($institution->email)<div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700"><span class="inline-flex items-center gap-2"><x-public.ui.icon name="mail" class="h-4 w-4" /> Email: {{ $institution->email }}</span></div>@endif
                        @if ($institution->website)<div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700"><span class="inline-flex items-center gap-2"><x-public.ui.icon name="globe" class="h-4 w-4" /> Website: {{ $institution->website }}</span></div>@endif
                        @if ($institution->city)<div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700"><span class="inline-flex items-center gap-2"><x-public.ui.icon name="map-pinned" class="h-4 w-4" /> Kota: {{ $institution->city }}</span></div>@endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
