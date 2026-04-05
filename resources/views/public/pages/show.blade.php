<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription">
    <article class="mx-auto max-w-4xl px-4 py-8 md:px-6 md:py-12">
        <div class="space-y-6 rounded-[32px] border border-white/60 bg-white/90 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)] md:p-8">
            <div class="space-y-3">
                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Halaman</div>
                <h1 class="text-3xl font-black tracking-tight text-slate-950 md:text-5xl">{{ $page->title }}</h1>
                @if ($page->excerpt)
                    <p class="text-base leading-7 text-slate-600 md:text-lg">{{ $page->excerpt }}</p>
                @endif
            </div>

            @if ($page->content)
                <div class="prose prose-slate max-w-none prose-headings:font-bold prose-a:text-[var(--site-primary)] prose-img:rounded-[24px]">
                    {!! $page->content !!}
                </div>
            @else
                <x-public.ui.empty-state title="Konten belum tersedia" description="Konten halaman ini akan muncul setelah dipublikasikan dari admin panel." />
            @endif
        </div>
    </article>
</x-layouts.public>
