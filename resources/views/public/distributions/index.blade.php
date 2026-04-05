<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription">
    <section class="mx-auto max-w-7xl space-y-8 px-4 py-8 md:px-6 md:py-12">
        <x-public.ui.section-header eyebrow="Transparansi" icon="hand-coins" :title="$pageTitle" :description="$pageDescription" />

        @if ($distributions->count() > 0)
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($distributions as $distribution)
                    <x-public.card.distribution-card :distribution="$distribution" />
                @endforeach
            </div>

            <div>{{ $distributions->links() }}</div>
        @else
            <x-public.ui.empty-state icon="hand-coins" title="Belum ada laporan penyaluran" description="Laporan penyaluran akan tampil saat program mulai direalisasikan." />
        @endif
    </section>
</x-layouts.public>
