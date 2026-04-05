@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'link' => null,
    'linkLabel' => null,
    'icon' => null,
])

<div class="flex items-end justify-between gap-4">
    <div class="max-w-2xl space-y-2">
        @if ($eyebrow)
            <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">
                @if ($icon)
                    <x-public.ui.icon :name="$icon" class="h-3.5 w-3.5" />
                @endif
                <span>{{ $eyebrow }}</span>
            </div>
        @endif

        <h2 class="text-2xl font-bold tracking-tight text-slate-950 md:text-3xl">{{ $title }}</h2>

        @if ($description)
            <p class="text-sm leading-6 text-slate-600 md:text-base">{{ $description }}</p>
        @endif
    </div>

    @if ($link && $linkLabel)
        <a href="{{ $link }}" class="hidden items-center gap-2 text-sm font-semibold text-slate-700 transition hover:text-slate-950 md:inline-flex">
            {{ $linkLabel }}
            <x-public.ui.icon name="arrow-right" class="h-4 w-4" />
        </a>
    @endif
</div>
