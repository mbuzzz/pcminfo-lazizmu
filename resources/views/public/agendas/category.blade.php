<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription">
    <section class="mx-auto max-w-7xl space-y-8 px-4 py-8 md:px-6 md:py-12">
        <div class="space-y-5 rounded-[32px] border border-white/60 bg-white/90 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)] md:p-8">
            <a href="{{ route('agendas.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:text-slate-950">
                <x-public.ui.icon name="arrow-left" class="h-4 w-4" />
                <span>Kembali ke semua agenda</span>
            </a>

            <div class="space-y-3">
                <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">
                    <x-public.ui.icon name="calendar-range" class="h-4 w-4" />
                    <span>Kategori Agenda</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-950 md:text-5xl">{{ $category->name }}</h1>
                <p class="max-w-3xl text-sm leading-7 text-slate-600 md:text-base">
                    {{ $category->description ?: 'Kategori ini menampilkan agenda dan kegiatan yang terkait dengan tema ' . $category->name . '.' }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('agendas.index') }}" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition border-slate-200 bg-white text-slate-700 hover:text-slate-950">
                <x-public.ui.icon name="layout-grid" class="h-4 w-4" />
                <span>Semua kategori</span>
            </a>
            @foreach ($categories as $item)
                <a href="{{ route('agendas.categories.show', $item) }}" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition {{ $item->is($category) ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-700 hover:text-slate-950' }}">
                    <x-public.ui.icon name="calendar-range" class="h-4 w-4" />
                    <span>{{ $item->name }}</span>
                    <span class="rounded-full bg-black/5 px-2 py-0.5 text-xs {{ $item->is($category) ? 'bg-white/15 text-white' : 'text-slate-500' }}">{{ number_format($item->agendas_count) }}</span>
                </a>
            @endforeach
        </div>

        @if ($agendas->count() > 0)
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($agendas as $agenda)
                    <x-public.card.agenda-card :agenda="$agenda" />
                @endforeach
            </div>

            <div>{{ $agendas->links() }}</div>
        @else
            <x-public.ui.empty-state icon="calendar-x-2" title="Belum ada agenda pada kategori ini" description="Agenda akan muncul di sini saat kategori ini sudah memiliki kegiatan aktif." />
        @endif
    </section>
</x-layouts.public>
