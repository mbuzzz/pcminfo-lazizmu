@props(['links'])

<div class="flex flex-col gap-2">
    @foreach ($links as $link)
        <a href="{{ $link['url'] }}" class="inline-flex items-center gap-2 text-sm text-slate-300 transition hover:text-white">
            <x-public.ui.icon name="chevron-right" class="h-4 w-4" />
            {{ $link['label'] }}
        </a>
    @endforeach
</div>
