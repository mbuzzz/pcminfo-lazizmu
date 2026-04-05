<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription">
    <section class="mx-auto max-w-7xl space-y-8 px-4 py-8 md:px-6 md:py-12">
        <x-public.ui.section-header
            eyebrow="PCM Info"
            icon="newspaper"
            :title="$pageTitle"
            :description="$pageDescription"
        />

        <form method="GET" class="grid gap-3 rounded-[28px] border border-white/60 bg-white/90 p-4 shadow-sm md:grid-cols-[1fr_220px_auto]">
            <label class="relative block">
                <x-public.ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari judul atau ringkasan berita"
                    class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm text-slate-900 outline-none ring-0 placeholder:text-slate-400 focus:border-slate-400"
                >
            </label>
            <select name="kategori" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected($currentCategory === $category->slug)>{{ $category->name }}</option>
                @endforeach
            </select>
            <x-public.ui.button type="submit" variant="primary" icon="sliders-horizontal" class="justify-center">Terapkan</x-public.ui.button>
        </form>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition {{ $currentCategory === '' ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-700 hover:text-slate-950' }}">
                <x-public.ui.icon name="layout-grid" class="h-4 w-4" />
                <span>Semua kategori</span>
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('posts.categories.show', $category) }}" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition {{ $currentCategory === $category->slug ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-700 hover:text-slate-950' }}">
                    <x-public.ui.icon name="bookmark" class="h-4 w-4" />
                    <span>{{ $category->name }}</span>
                    <span class="rounded-full bg-black/5 px-2 py-0.5 text-xs {{ $currentCategory === $category->slug ? 'bg-white/15 text-white' : 'text-slate-500' }}">{{ number_format($category->posts_count) }}</span>
                </a>
            @endforeach
        </div>

        @if ($posts->count() > 0)
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($posts as $post)
                    <x-public.card.news-card :post="$post" />
                @endforeach
            </div>

            <div>{{ $posts->links() }}</div>
        @else
            <x-public.ui.empty-state icon="newspaper" title="Belum ada berita yang sesuai" description="Coba ubah kata kunci pencarian atau pilih kategori lain." />
        @endif
    </section>
</x-layouts.public>
