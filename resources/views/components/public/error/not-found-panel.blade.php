<section class="mx-auto flex min-h-[72vh] max-w-5xl items-center px-4 py-10 md:px-6 md:py-16">
    <div class="relative w-full overflow-hidden rounded-[36px] border border-white/60 bg-white/90 p-8 text-center shadow-[0_24px_70px_rgba(15,23,42,0.12)] md:p-12">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(44,54,139,0.08),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(188,208,59,0.12),transparent_26%)]"></div>

        <div class="relative z-10">
            <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100 text-slate-700 shadow-inner">
                <x-public.ui.icon name="compass" class="h-8 w-8" />
            </div>

            <div class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-500">404</div>
            <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-950 md:text-5xl">Halaman ini belum ditemukan</h1>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-slate-600 md:text-base">
                Tautan yang Anda buka mungkin sudah berubah, atau halaman tersebut memang belum tersedia untuk publik.
            </p>
            <p class="mx-auto mt-3 max-w-xl text-sm italic text-slate-500">
                Setiap langkah yang baik tetap menemukan jalannya. Mari kembali ke halaman yang tersedia.
            </p>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <x-public.ui.button :href="route('home')" variant="primary" icon="house">
                    Kembali ke Beranda
                </x-public.ui.button>
                <x-public.ui.button :href="route('campaigns.index')" variant="donation" icon="heart-handshake">
                    Lihat Program Donasi
                </x-public.ui.button>
                <x-public.ui.button :href="route('posts.index')" variant="secondary" icon="newspaper">
                    Baca Berita
                </x-public.ui.button>
            </div>
        </div>
    </div>
</section>
