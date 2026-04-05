@props([
    'value' => request('q', ''),
])

<form action="{{ route('search.index') }}" method="GET" {{ $attributes->class('flex items-center gap-2') }}>
    <label class="relative block grow">
        <input
            type="search"
            name="q"
            value="{{ $value }}"
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
