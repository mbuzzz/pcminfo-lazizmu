<div class="flex items-center gap-3">
    @if ($logoUrl)
        <img
            src="{{ $logoUrl }}"
            alt="Logo {{ $siteName }}"
            class="h-11 w-11 rounded-xl object-cover"
        >
    @endif

    <div class="min-w-0">
        <div class="truncate text-sm font-semibold text-gray-950 dark:text-white">
            {{ $siteName }}
        </div>
    </div>
</div>
