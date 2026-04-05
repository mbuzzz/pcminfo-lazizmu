@props([
    'name',
])

<x-dynamic-component :component="'lucide-' . $name" {{ $attributes->class(['shrink-0']) }} />
