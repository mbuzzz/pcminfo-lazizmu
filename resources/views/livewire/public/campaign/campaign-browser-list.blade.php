<div class="space-y-8">
    <x-public.ui.section-header eyebrow="Lazismu" icon="heart-handshake" :title="$heading" :description="$description" />

    <div class="grid gap-3 rounded-[28px] border border-white/60 bg-white/90 p-4 shadow-sm md:grid-cols-[1fr_220px_240px_auto]">
        <label class="relative block">
            <x-public.ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari judul atau deskripsi program"
                class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm text-slate-900 outline-none ring-0 placeholder:text-slate-400 focus:border-slate-400"
            >
        </label>

        <select wire:model.live="type" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400">
            <option value="">Semua jenis</option>
            @foreach (\App\Enums\CampaignType::cases() as $typeOption)
                <option value="{{ $typeOption->value }}">{{ $typeOption->getLabel() }}</option>
            @endforeach
        </select>

        <select wire:model.live="category" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400">
            <option value="">Semua kategori</option>
            @foreach ($categories as $item)
                <option value="{{ $item->slug }}">{{ $item->name }}</option>
            @endforeach
        </select>

        <x-public.ui.button wire:click="clearFilters" variant="secondary" icon="rotate-ccw" class="justify-center">
            Reset
        </x-public.ui.button>
    </div>

    <div class="flex flex-wrap gap-3">
        <button wire:click="$set('category', '')" class="{{ $category === '' ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-700 hover:text-slate-950' }} inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition">
            <x-public.ui.icon name="layout-grid" class="h-4 w-4" />
            <span>Semua kategori</span>
        </button>
        @foreach ($categories as $item)
            <button wire:click="$set('category', '{{ $item->slug }}')" class="{{ $category === $item->slug ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-700 hover:text-slate-950' }} inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition">
                <x-public.ui.icon name="bookmark" class="h-4 w-4" />
                <span>{{ $item->name }}</span>
                <span class="rounded-full bg-black/5 px-2 py-0.5 text-xs {{ $category === $item->slug ? 'bg-white/15 text-white' : 'text-slate-500' }}">{{ number_format($item->campaigns_count) }}</span>
            </button>
        @endforeach
    </div>

    @if ($campaigns->count() > 0)
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($campaigns as $campaign)
                <x-public.card.campaign-card :campaign="$campaign" />
            @endforeach
        </div>

        <div>{{ $campaigns->links() }}</div>
    @else
        <x-public.ui.empty-state icon="heart-crack" title="Belum ada program yang sesuai" description="Coba ubah jenis, kategori, atau kata kunci pencarian." />
    @endif
</div>
