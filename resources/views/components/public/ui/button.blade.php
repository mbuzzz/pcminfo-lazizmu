@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'leading',
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 rounded-full font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';
    $sizeClasses = match ($size) {
        'sm' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3.5 text-base',
        default => 'px-5 py-3 text-sm',
    };
    $variantClasses = match ($variant) {
        'secondary' => 'border border-slate-200 bg-white text-slate-900 hover:-translate-y-0.5 hover:shadow-lg focus:ring-slate-300',
        'ghost' => 'text-slate-700 hover:bg-slate-100 focus:ring-slate-300',
        'donation' => 'bg-[linear-gradient(135deg,#E8242A_0%,#F1B12D_100%)] text-white shadow-[0_18px_40px_rgba(232,36,42,0.22)] hover:-translate-y-0.5 hover:shadow-[0_22px_50px_rgba(232,36,42,0.28)] focus:ring-orange-300',
        default => 'text-white shadow-[0_16px_34px_rgba(44,54,139,0.20)] hover:-translate-y-0.5 hover:shadow-[0_22px_42px_rgba(44,54,139,0.24)] focus:ring-indigo-300',
    };
    $style = $variant === 'primary' ? 'background: linear-gradient(135deg, var(--site-primary) 0%, var(--site-secondary) 100%);' : null;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class([$baseClasses, $sizeClasses, $variantClasses])->merge(['style' => $style]) }}>
        @if ($icon && $iconPosition === 'leading')
            <x-public.ui.icon :name="$icon" class="h-4 w-4" />
        @endif
        {{ $slot }}
        @if ($icon && $iconPosition === 'trailing')
            <x-public.ui.icon :name="$icon" class="h-4 w-4" />
        @endif
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button'])->class([$baseClasses, $sizeClasses, $variantClasses])->merge(['style' => $style]) }}>
        @if ($icon && $iconPosition === 'leading')
            <x-public.ui.icon :name="$icon" class="h-4 w-4" />
        @endif
        {{ $slot }}
        @if ($icon && $iconPosition === 'trailing')
            <x-public.ui.icon :name="$icon" class="h-4 w-4" />
        @endif
    </button>
@endif
