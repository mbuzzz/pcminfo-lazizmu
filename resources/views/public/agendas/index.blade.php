<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription">
    <section class="mx-auto max-w-7xl space-y-8 px-4 py-8 md:px-6 md:py-12">
        <x-public.ui.section-header eyebrow="Agenda" icon="calendar-range" :title="$pageTitle" :description="$pageDescription" />

        @if ($categories->isNotEmpty())
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('agendas.index') }}" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition {{ $currentCategory === '' ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-700 hover:text-slate-950' }}">
                    <x-public.ui.icon name="layout-grid" class="h-4 w-4" />
                    <span>Semua kategori</span>
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('agendas.categories.show', $category) }}" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition {{ $currentCategory === $category->slug ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-700 hover:text-slate-950' }}">
                        <x-public.ui.icon name="calendar-range" class="h-4 w-4" />
                        <span>{{ $category->name }}</span>
                        <span class="rounded-full bg-black/5 px-2 py-0.5 text-xs {{ $currentCategory === $category->slug ? 'bg-white/15 text-white' : 'text-slate-500' }}">{{ number_format($category->agendas_count) }}</span>
                    </a>
                @endforeach
            </div>
        @endif

        @if ($agendas->count() > 0)
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($agendas as $agenda)
                    <x-public.card.agenda-card :agenda="$agenda" />
                @endforeach
            </div>

            <div>{{ $agendas->links() }}</div>
        @else
            <x-public.ui.empty-state icon="calendar-x-2" title="Belum ada agenda" description="Agenda yang sudah dipublikasikan akan muncul di sini." />
        @endif
    </section>
</x-layouts.public>
