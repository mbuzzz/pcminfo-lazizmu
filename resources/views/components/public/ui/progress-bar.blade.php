@props([
    'percentage' => 0,
    'color' => 'var(--site-primary)',
])

@php
    $percentage = max(0, min(100, (float) $percentage));
@endphp

<div class="space-y-2">
    <div class="h-3 overflow-hidden rounded-full bg-slate-200">
        <div
            class="h-full rounded-full transition-all duration-500"
            style="width: {{ $percentage }}%; background: linear-gradient(90deg, {{ $color }} 0%, var(--site-accent) 100%);"
        ></div>
    </div>
    <div class="text-xs font-semibold text-slate-500">{{ number_format($percentage, 1, ',', '.') }}% tercapai</div>
</div>
