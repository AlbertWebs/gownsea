@props([
    'title' => '',
    'subtitle' => null,
    'ctaLabel' => 'Shop Now',
    'ctaHref' => '#shop',
    'image' => '',
    'alt' => null,
])

<section
    class="page-banner"
    x-data="{ ready: false }"
    :class="{ 'is-ready': ready }"
>
    <img
        src="{{ $image }}"
        alt="{{ $alt ?? $title }}"
        width="1920"
        height="900"
        fetchpriority="high"
        decoding="async"
        class="page-banner__image"
        x-init="if ($el.complete) { ready = true }"
        @load="ready = true"
    >
    <div class="page-banner__overlay" aria-hidden="true"></div>
    <div class="page-banner__content">
        <h1 class="page-banner__title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="page-banner__subtitle">{{ $subtitle }}</p>
        @endif
        @if ($ctaLabel && $ctaHref)
            <a href="{{ $ctaHref }}" class="page-banner__cta">{{ $ctaLabel }}</a>
        @endif
    </div>
</section>
