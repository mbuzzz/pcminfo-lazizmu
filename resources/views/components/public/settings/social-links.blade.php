@props(['links'])

<div class="flex flex-wrap gap-2">
    @foreach ($links as $platform => $url)
        @if ($url)
            <a
                href="{{ $url }}"
                target="_blank"
                rel="noreferrer"
                class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:-translate-y-0.5 hover:border-slate-300 hover:text-slate-950"
            >
                <x-public.ui.icon :name="match($platform) { 'instagram' => 'instagram', 'facebook' => 'facebook', 'youtube' => 'youtube', 'tiktok' => 'music-2', default => 'globe' }" class="h-4 w-4" />
                {{ ucfirst($platform) }}
            </a>
        @endif
    @endforeach
</div>
