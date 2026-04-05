@php
    $meta = $siteSettings->seo($title ?? null, $description ?? null, $image ?? null);
@endphp

<meta name="description" content="{{ $meta['description'] ?? '' }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $meta['title'] ?? '' }}">
<meta property="og:description" content="{{ $meta['description'] ?? '' }}">

@if (! empty($meta['image']))
    <meta property="og:image" content="{{ $meta['image'] }}">
    <meta name="twitter:image" content="{{ $meta['image'] }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $meta['title'] ?? '' }}">
<meta name="twitter:description" content="{{ $meta['description'] ?? '' }}">

@if (! empty($meta['verification_code']))
    {!! $meta['verification_code'] !!}
@endif
