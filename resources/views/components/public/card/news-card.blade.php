@props(['post'])

<article class="group rounded-[28px] border border-white/60 bg-white/90 p-4 shadow-[0_12px_32px_rgba(15,23,42,0.08)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_20px_44px_rgba(15,23,42,0.12)]">
    <a href="{{ route('posts.show', $post) }}" class="block">
        <div class="aspect-[16/11] overflow-hidden rounded-[24px] bg-slate-100">
            @if ($post->featured_image_url)
                <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy">
            @else
                <div class="flex h-full items-center justify-center bg-[radial-gradient(circle_at_top_left,rgba(44,54,139,0.18),transparent_55%),linear-gradient(135deg,#eef2ff,#f8fafc)] text-sm font-semibold text-slate-500">Artikel PCM</div>
            @endif
        </div>
    </a>

    <div class="space-y-3 px-1 pt-4">
        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
            @if ($post->category)
                <a href="{{ route('posts.categories.show', $post->category) }}" class="relative z-10 inline-flex items-center gap-1.5 transition hover:text-slate-700">
                    <x-public.ui.icon name="bookmark" class="h-3.5 w-3.5" />
                    <span>{{ $post->category->name }}</span>
                </a>
            @endif
            @if ($post->published_at)
                <span class="inline-flex items-center gap-1.5"><x-public.ui.icon name="calendar-days" class="h-3.5 w-3.5" /> {{ $post->published_at->translatedFormat('d M Y') }}</span>
            @endif
        </div>

        <a href="{{ route('posts.show', $post) }}" class="block">
            <h3 class="line-clamp-2 text-lg font-bold tracking-tight text-slate-950 transition group-hover:text-[var(--site-primary)]">{{ $post->title }}</h3>
        </a>

        @if ($post->excerpt)
            <p class="line-clamp-3 text-sm leading-6 text-slate-600">{{ $post->excerpt }}</p>
        @endif

        <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--site-primary)]">
            <span>Baca selengkapnya</span>
            <x-public.ui.icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>
</article>
