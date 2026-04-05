@props([
    'title' => 'Belum ada data',
    'description' => 'Konten akan tampil setelah data tersedia.',
    'icon' => 'inbox',
])

<div class="rounded-[28px] border border-dashed border-slate-300 bg-white/80 px-6 py-10 text-center shadow-sm">
    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-500">
        <x-public.ui.icon :name="$icon" class="h-6 w-6" />
    </div>
    <h3 class="text-lg font-semibold text-slate-950">{{ $title }}</h3>
    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $description }}</p>
</div>
