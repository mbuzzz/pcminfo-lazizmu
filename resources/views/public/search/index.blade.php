<x-layouts.public page-title="Pencarian" page-description="Cari berita, agenda, program, dan direktori organisasi dari satu halaman.">
    <section class="mx-auto max-w-7xl px-4 py-8 md:px-6 md:py-10">
        <livewire:public.search.global-search-page :initial-search="request('q', '')" :initial-type="request('tipe', 'all')" />
    </section>
</x-layouts.public>
