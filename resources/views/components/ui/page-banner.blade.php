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
    <div class="page-banner__glow" aria-hidden="true"></div>
    <div class="page-banner__sheen" aria-hidden="true"></div>
    <div class="page-banner__grain" aria-hidden="true"></div>
    <div class="page-banner__content">
        <h1 class="page-banner__title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="page-banner__subtitle">{{ $subtitle }}</p>
        @endif
        @if ($ctaLabel && $ctaHref)
            <a href="{{ $ctaHref }}" class="page-banner__cta">
                <span>{{ $ctaLabel }}</span>
                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        @endif
    </div>
</section>
