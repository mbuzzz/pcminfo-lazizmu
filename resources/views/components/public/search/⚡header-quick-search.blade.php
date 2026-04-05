<?php

use App\Enums\AgendaStatus;
use App\Models\Agenda;
use App\Models\Campaign;
use App\Models\Institution;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $query = '';

    #[Computed]
    public function hasQuery(): bool
    {
        return mb_strlen(trim($this->query)) >= 2;
    }

    #[Computed]
    public function postResults(): Collection
    {
        if (! $this->hasQuery()) {
            return collect();
        }

        return Post::query()
            ->published()
            ->with('category')
            ->where(function (Builder $query): void {
                $query
                    ->where('title', 'like', '%' . $this->query . '%')
                    ->orWhere('excerpt', 'like', '%' . $this->query . '%')
                    ->orWhere('content', 'like', '%' . $this->query . '%');
            })
            ->latest('published_at')
            ->limit(3)
            ->get();
    }

    #[Computed]
    public function agendaResults(): Collection
    {
        if (! $this->hasQuery()) {
            return collect();
        }

        return Agenda::query()
            ->with('category')
            ->where('status', AgendaStatus::Published)
            ->where(function (Builder $query): void {
                $query
                    ->where('title', 'like', '%' . $this->query . '%')
                    ->orWhere('description', 'like', '%' . $this->query . '%')
                    ->orWhere('location_name', 'like', '%' . $this->query . '%')
                    ->orWhere('location_address', 'like', '%' . $this->query . '%');
            })
            ->orderBy('start_at')
            ->limit(3)
            ->get();
    }

    #[Computed]
    public function campaignResults(): Collection
    {
        if (! $this->hasQuery()) {
            return collect();
        }

        return Campaign::query()
            ->with('category')
            ->whereIn('status', ['active', 'completed'])
            ->where(function (Builder $query): void {
                $query
                    ->where('title', 'like', '%' . $this->query . '%')
                    ->orWhere('short_description', 'like', '%' . $this->query . '%')
                    ->orWhere('description', 'like', '%' . $this->query . '%');
            })
            ->orderByDesc('is_featured')
            ->latest()
            ->limit(3)
            ->get();
    }

    #[Computed]
    public function institutionResults(): Collection
    {
        if (! $this->hasQuery()) {
            return collect();
        }

        return Institution::query()
            ->active()
            ->where(function (Builder $query): void {
                $query
                    ->where('name', 'like', '%' . $this->query . '%')
                    ->orWhere('tagline', 'like', '%' . $this->query . '%')
                    ->orWhere('description', 'like', '%' . $this->query . '%')
                    ->orWhere('address', 'like', '%' . $this->query . '%');
            })
            ->orderBy('name')
            ->limit(3)
            ->get();
    }

    #[Computed]
    public function hasResults(): bool
    {
        return $this->postResults->isNotEmpty()
            || $this->agendaResults->isNotEmpty()
            || $this->campaignResults->isNotEmpty()
            || $this->institutionResults->isNotEmpty();
    }

    #[Computed]
    public function totalResults(): int
    {
        return $this->postResults->count()
            + $this->agendaResults->count()
            + $this->campaignResults->count()
            + $this->institutionResults->count();
    }
};
?>

<div
    x-data="{ open: false }"
    x-on:click.outside="open = false"
    x-on:keydown.escape.window="open = false"
    class="relative"
>
    <form action="{{ route('search.index') }}" method="GET" class="flex items-center gap-2" x-on:submit="open = false">
        <label class="relative block grow">
            <input
                type="search"
                name="q"
                wire:model.live.debounce.250ms="query"
                x-on:focus="open = true"
                x-on:input="open = true"
                value="{{ request('q', '') }}"
                placeholder="Cari berita, agenda, program..."
                class="w-full rounded-full border border-slate-200 bg-white/92 py-3 pl-4 pr-14 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-300 focus:outline-none"
            >

            <button
                type="submit"
                class="absolute right-1.5 top-1/2 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-slate-950 text-white shadow-sm transition hover:bg-slate-800"
                aria-label="Cari"
            >
                <x-public.ui.icon name="search" class="h-4 w-4" />
            </button>
        </label>
    </form>

    <div
        x-show="open && $wire.hasQuery"
        x-cloak
        x-transition
        class="absolute left-0 right-0 top-[calc(100%+0.85rem)] z-50 overflow-hidden rounded-[28px] border border-white/70 bg-white/95 shadow-[0_24px_60px_rgba(15,23,42,0.16)] backdrop-blur-xl"
    >
        <div class="border-b border-slate-200/80 px-4 py-3">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-bold text-slate-950">Hasil cepat pencarian</div>
                    <div class="text-xs text-slate-500">Menampilkan hasil terdekat dari seluruh modul publik.</div>
                </div>
                <a href="{{ route('search.index', ['q' => $query]) }}" class="inline-flex items-center gap-2 rounded-full bg-slate-950 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800" wire:navigate>
                    <span>Lihat semua</span>
                    <x-public.ui.icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            </div>
        </div>

        @if ($this->hasResults)
            <div class="grid gap-0 md:grid-cols-2">
                @foreach ([
                    'Berita' => $this->postResults,
                    'Agenda' => $this->agendaResults,
                    'Program' => $this->campaignResults,
                    'Amal Usaha' => $this->institutionResults,
                ] as $sectionLabel => $items)
                    <div class="border-b border-slate-200/70 p-4 md:border-r even:md:border-r-0 [&:nth-last-child(-n+2)]:md:border-b-0">
                        <div class="mb-3 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">
                            {{ $sectionLabel }}
                            <span class="ml-1 text-slate-500">({{ $items->count() }})</span>
                        </div>

                        @if ($items->isNotEmpty())
                            <div class="space-y-2.5">
                                @foreach ($items as $item)
                                    @php
                                        $route = match ($sectionLabel) {
                                            'Berita' => route('posts.show', $item),
                                            'Agenda' => route('agendas.show', $item),
                                            'Program' => route('campaigns.show', $item),
                                            'Amal Usaha' => route('institutions.show', $item),
                                        };
                                        $description = match ($sectionLabel) {
                                            'Berita' => $item->excerpt,
                                            'Agenda' => $item->description,
                                            'Program' => $item->short_description,
                                            'Amal Usaha' => $item->tagline ?: $item->description,
                                        };
                                    @endphp

                                    <a href="{{ $route }}" class="block rounded-2xl border border-slate-200/70 bg-slate-50/80 px-3 py-3 transition hover:border-slate-300 hover:bg-white" wire:navigate x-on:click="open = false">
                                        <div class="line-clamp-1 text-sm font-bold text-slate-950">{{ $item->title ?? $item->name }}</div>
                                        @if (filled($description))
                                            <div class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ \Illuminate\Support\Str::limit(strip_tags((string) $description), 90) }}</div>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-2xl bg-slate-50 px-3 py-4 text-xs text-slate-500">
                                Belum ada hasil pada modul ini.
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-5">
                <x-public.ui.empty-state
                    icon="search-x"
                    title="Belum ada hasil yang cocok"
                    description="Coba kata kunci yang lebih umum atau buka halaman pencarian penuh untuk eksplorasi lebih luas."
                />
            </div>
        @endif
    </div>
</div>
