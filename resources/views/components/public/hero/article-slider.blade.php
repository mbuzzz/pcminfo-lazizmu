@props([
    'posts',
])

<div
    x-data="{
        active: 0,
        total: {{ max($posts->count(), 1) }},
        touchStart: 0,
        autoplay: null,
        next() { this.active = (this.active + 1) % this.total },
        prev() { this.active = (this.active - 1 + this.total) % this.total },
        startAutoplay() {
            if (this.total <= 1) return;
            this.autoplay = setInterval(() => this.next(), 6500);
        },
        stopAutoplay() {
            if (this.autoplay) clearInterval(this.autoplay);
        },
        onTouchStart(event) {
            this.touchStart = event.changedTouches[0].clientX;
        },
        onTouchEnd(event) {
            const touchEnd = event.changedTouches[0].clientX;
            const delta = touchEnd - this.touchStart;
            if (Math.abs(delta) < 40) return;
            delta < 0 ? this.next() : this.prev();
        },
        init() {
            this.startAutoplay();
        }
    }"
    x-on:mouseenter="stopAutoplay()"
    x-on:mouseleave="startAutoplay()"
    x-on:focusin="stopAutoplay()"
    x-on:focusout="startAutoplay()"
    x-on:touchstart="onTouchStart($event)"
    x-on:touchend="onTouchEnd($event)"
    class="relative flex h-full flex-col overflow-hidden rounded-[34px] border border-white/60 bg-slate-950 shadow-[0_30px_80px_rgba(15,23,42,0.22)]"
>
    @if ($posts->isNotEmpty())
        <div class="absolute inset-x-0 top-0 z-20 flex items-center justify-between px-5 pt-5 md:px-7">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.24em] text-white/85 backdrop-blur">
                <x-public.ui.icon name="sparkles" class="h-3.5 w-3.5" />
                Artikel Terbaru
            </div>

            <div class="hidden items-center gap-2 sm:flex">
                <button type="button" x-on:click="prev()" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white backdrop-blur transition hover:bg-white/15">
                    <x-public.ui.icon name="arrow-left" class="h-4 w-4" />
                </button>
                <button type="button" x-on:click="next()" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/15 bg-white/10 text-white backdrop-blur transition hover:bg-white/15">
                    <x-public.ui.icon name="arrow-right" class="h-4 w-4" />
                </button>
            </div>
        </div>

        <div class="relative flex-1 min-h-[480px] sm:min-h-[420px] lg:min-h-[380px] xl:min-h-[400px]">
            @foreach ($posts as $index => $post)
                <article
                    x-show="active === {{ $index }}"
                    x-cloak
                    x-transition.opacity.duration.500ms
                    class="absolute inset-0"
                >
                    @if ($post->featured_image_url)
                        <img
                            src="{{ $post->featured_image_url }}"
                            alt="{{ $post->title }}"
                            class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-45 transition duration-700"
                            loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                            decoding="async"
                            fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}"
                        >
                    @endif

                    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(135deg,rgba(15,23,42,0.78)_0%,rgba(15,23,42,0.58)_50%,rgba(16,129,111,0.48)_100%)]"></div>
                    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_15%_20%,rgba(250,237,33,0.18),transparent_24%),radial-gradient(circle_at_84%_22%,rgba(255,255,255,0.12),transparent_22%),radial-gradient(circle_at_75%_74%,rgba(232,36,42,0.18),transparent_26%)]"></div>
                    <div class="pointer-events-none absolute -left-16 bottom-8 h-44 w-44 rounded-full bg-white/10 blur-3xl"></div>
                    <div class="pointer-events-none absolute right-10 top-20 hidden h-32 w-32 rounded-full border border-white/12 bg-white/6 blur-0 md:block"></div>
                    <div class="pointer-events-none absolute bottom-7 right-7 hidden rounded-[22px] border border-white/20 bg-white/10 px-4 py-2.5 text-white/90 shadow-[0_12px_32px_rgba(15,23,42,0.14)] backdrop-blur-md xl:block">
                        <div class="text-[9px] font-bold uppercase tracking-[0.22em] text-white/50">Sorotan Edisi</div>
                        <div class="mt-1.5 max-w-[10rem] text-xs font-bold leading-5">{{ $post->category?->name ?? 'Berita Organisasi' }}</div>
                    </div>

                    <div class="relative z-10 flex h-full flex-col justify-between px-5 pb-16 pt-20 md:px-7 pointer-events-auto">
                        <div class="flex justify-end">
                            <div class="hidden max-w-[12rem] rounded-[22px] border border-white/20 bg-white/10 px-4 py-2.5 text-left text-white shadow-[0_12px_32px_rgba(15,23,42,0.14)] backdrop-blur-md xl:block">
                                <div class="text-[9px] font-bold uppercase tracking-[0.22em] text-white/50">Update Cepat</div>
                                <div class="mt-1.5 text-xs font-bold leading-4.5 opacity-90">Artikel pilihan dari isu utama gerakan pcm.</div>
                            </div>
                        </div>

                        <div class="max-w-3xl space-y-4">
                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-white/75">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 backdrop-blur">
                                    <x-public.ui.icon name="newspaper" class="h-3.5 w-3.5" />
                                    {{ $post->category?->name ?? 'Berita' }}
                                </span>
                                @if ($post->published_at)
                                    <span>{{ $post->published_at->translatedFormat('d M Y') }}</span>
                                @endif
                            </div>

                            <div class="space-y-2 md:space-y-3">
                                <h1 class="line-clamp-3 max-w-2xl text-2xl font-black tracking-tight text-white sm:text-3xl md:line-clamp-2 md:text-5xl xl:text-5xl xl:leading-tight">{{ $post->title }}</h1>
                                @if ($post->excerpt)
                                    <p class="line-clamp-2 max-w-2xl text-sm leading-6 text-white/80 md:line-clamp-3 md:text-base md:leading-7">{{ $post->excerpt }}</p>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-2 sm:gap-3">
                                <x-public.ui.button :href="route('posts.show', $post)" variant="secondary" icon="arrow-right" icon-position="trailing">
                                    Baca Selengkapnya
                                </x-public.ui.button>
                                <x-public.ui.button :href="route('posts.index')" variant="ghost" class="text-white hover:bg-white/10">
                                    Semua Berita
                                </x-public.ui.button>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="absolute inset-x-0 bottom-0 z-20 flex items-center justify-between gap-4 px-5 pb-5 md:px-7">
            <div class="flex items-center gap-2">
                @foreach ($posts as $index => $post)
                    <button
                        type="button"
                        x-on:click="active = {{ $index }}"
                        class="transition"
                        aria-label="Pindah ke slide {{ $index + 1 }}"
                    >
                        <span
                            class="block rounded-full"
                            :class="active === {{ $index }} ? 'h-2.5 w-8 bg-white' : 'h-2.5 w-2.5 bg-white/40'"
                        ></span>
                    </button>
                @endforeach
            </div>

            <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.22em] text-white/85 backdrop-blur sm:hidden">
                <x-public.ui.icon name="move-horizontal" class="h-3.5 w-3.5" />
                Geser
            </div>
        </div>
    @else
        <div class="p-6 md:p-8">
            <x-public.ui.empty-state
                icon="newspaper"
                title="Belum ada artikel unggulan"
                description="Artikel terbaru akan tampil di area hero setelah dipublikasikan dari panel admin."
            />
        </div>
    @endif
</div>
