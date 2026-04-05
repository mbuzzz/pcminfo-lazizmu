<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription">
    <section class="mx-auto max-w-7xl space-y-8 px-4 py-8 md:px-6 md:py-12">
        <x-public.ui.section-header eyebrow="Organisasi" icon="building-2" :title="$pageTitle" :description="$pageDescription" />

        @if ($institutions->count() > 0)
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($institutions as $institution)
                    <x-public.card.institution-card :institution="$institution" />
                @endforeach
            </div>

            <div>{{ $institutions->links() }}</div>
        @else
            <x-public.ui.empty-state icon="building-2" title="Belum ada data amal usaha" description="Direktori akan tampil setelah data diaktifkan dari backend." />
        @endif
    </section>
</x-layouts.public>
