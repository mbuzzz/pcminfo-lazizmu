<x-layouts.public :page-title="$pageTitle" :page-description="$pageDescription">
    <section class="mx-auto max-w-7xl space-y-8 px-4 py-8 md:px-6 md:py-12">
        <div class="space-y-5 rounded-[32px] border border-white/60 bg-white/90 p-6 shadow-[0_16px_42px_rgba(15,23,42,0.10)] md:p-8">
            <a href="{{ route('campaigns.index') }}" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:text-slate-950">
                <x-public.ui.icon name="arrow-left" class="h-4 w-4" />
                <span>Kembali ke semua program</span>
            </a>

            <div class="space-y-3">
                <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">
                    <x-public.ui.icon name="heart-handshake" class="h-4 w-4" />
                    <span>Kategori Program</span>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-950 md:text-5xl">{{ $category->name }}</h1>
                <p class="max-w-3xl text-sm leading-7 text-slate-600 md:text-base">
                    {{ $category->description ?: 'Program filantropi pada kategori ' . $category->name . '.' }}
                </p>
            </div>
        </div>

        <livewire:public.campaign.campaign-browser-list
            :heading="$pageTitle"
            :description="$pageDescription"
            :initial-category="$category->slug"
            :category-title="$category->name"
        />
    </section>
</x-layouts.public>
