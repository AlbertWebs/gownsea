@props([
    'property',
])

@php
    $slug = $property['slug'] ?? 'product';
    $image = $property['image'] ?? '/images/site/hero.webp';
    $gallery = $property['gallery'] ?? [];
    $thumb = $gallery[0] ?? $image;
    $title = $property['title'] ?? 'Featured attire';
    $price = $property['price'] ?? 'Request quote';
    $href = $property['url'] ?? route('products.show', $slug);
    $quote = route('products.show', $slug).'#request-quote';
@endphp

<article {{ $attributes->class('featured-tile') }}>
    <a href="{{ $href }}" class="featured-tile__media" aria-label="{{ $title }}">
        <img src="{{ $image }}" alt="{{ $title }}" loading="lazy" decoding="async">
    </a>

    <div class="featured-tile__bar">
        <a href="{{ $href }}" class="featured-tile__thumb" tabindex="-1" aria-hidden="true">
            <img src="{{ $thumb }}" alt="" loading="lazy" decoding="async">
        </a>

        <div class="featured-tile__copy">
            <a href="{{ $href }}" class="featured-tile__name">{{ $title }}</a>
            <p class="featured-tile__price">{{ $price }}</p>
        </div>

        <a
            href="{{ $quote }}"
            class="featured-tile__add"
            aria-label="Request quote for {{ $title }}"
            title="Request quote"
        >
            <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M8 3.2v9.6M3.2 8h9.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </a>
    </div>
</article>
