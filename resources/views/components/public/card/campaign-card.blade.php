@props(['campaign'])

@php
    $featuredImage = $campaign->featured_image_url;
@endphp

<article class="group overflow-hidden rounded-[30px] border border-white/60 bg-white/95 shadow-[0_14px_36px_rgba(15,23,42,0.09)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_22px_48px_rgba(15,23,42,0.13)]">
    <a href="{{ route('campaigns.show', $campaign) }}" class="block">
        <div class="relative aspect-[16/11] overflow-hidden bg-slate-100">
            @if ($featuredImage)
                <img src="{{ $featuredImage }}" alt="{{ $campaign->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy">
            @else
                <div class="absolute inset-0 bg-[linear-gradient(135deg,#fee2e2_0%,#fef3c7_100%)]"></div>
            @endif

            <div class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-700 backdrop-blur">
                {{ $campaign->type->getLabel() }}
            </div>
        </div>
    </a>

    <div class="space-y-4 p-5">
        <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                @if ($campaign->category)
                    <a href="{{ route('campaigns.categories.show', $campaign->category) }}" class="relative z-10 inline-flex items-center gap-1.5 transition hover:text-slate-700">
                        <x-public.ui.icon name="bookmark" class="h-3.5 w-3.5" />
                        <span>{{ $campaign->category->name }}</span>
                    </a>
                @endif
            </div>
            <a href="{{ route('campaigns.show', $campaign) }}" class="block">
                <h3 class="line-clamp-2 text-xl font-bold tracking-tight text-slate-950 transition group-hover:text-red-600">{{ $campaign->title }}</h3>
            </a>
            @if ($campaign->short_description)
                <p class="line-clamp-3 text-sm leading-6 text-slate-600">{{ $campaign->short_description }}</p>
            @endif
        </div>

        <x-public.ui.progress-bar :percentage="$campaign->progress_percentage" color="#E8242A" />

        <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-2xl bg-slate-50 p-3">
                <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Terkumpul</div>
                <div class="mt-2 font-bold text-slate-950">
                    {{ $campaign->goal_type === 'nominal' ? $campaign->getFormattedCollectedAmount() : number_format($campaign->collected_unit) . ' ' . $campaign->unit_label }}
                </div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-3">
                <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Target</div>
                <div class="mt-2 font-bold text-slate-950">
                    {{ $campaign->goal_type === 'nominal' ? $campaign->getFormattedGoalAmount() : number_format($campaign->goal_unit ?? 0) . ' ' . $campaign->unit_label }}
                </div>
            </div>
        </div>

        <a href="{{ route('campaigns.show', $campaign) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-red-600">
            <span>Lihat program</span>
            <x-public.ui.icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>
</article>
