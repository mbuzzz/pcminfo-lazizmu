<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription">
    <section class="mx-auto max-w-7xl space-y-8 px-4 py-8 md:px-6 md:py-12">
        <x-public.ui.section-header eyebrow="E-Struktur" icon="users" :title="$pageTitle" :description="$pageDescription" />

        <form method="GET" class="grid gap-3 rounded-[28px] border border-white/60 bg-white/90 p-4 shadow-sm md:grid-cols-[260px_auto]">
            <select name="periode" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400">
                <option value="">Semua periode</option>
                @foreach ($periods as $period)
                    <option value="{{ $period }}" @selected($selectedPeriod === $period)>{{ $period }}</option>
                @endforeach
            </select>
            <x-public.ui.button type="submit" variant="primary" icon="sliders-horizontal" class="justify-center md:w-max">Terapkan</x-public.ui.button>
        </form>

        @if ($leaders->count() > 0)
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($leaders as $leader)
                    <x-public.card.leader-card :leader="$leader" />
                @endforeach
            </div>

            <div>{{ $leaders->links() }}</div>
        @else
            <x-public.ui.empty-state icon="users" title="Belum ada data pimpinan" description="Struktur pimpinan akan tampil setelah data aktif tersedia." />
        @endif
    </section>
</x-layouts.public>
