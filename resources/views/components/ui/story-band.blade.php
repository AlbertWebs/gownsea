@props([
    'kicker' => null,
    'title' => '',
    'description' => '',
    'ctaLabel' => 'Shop now',
    'ctaHref' => '#',
    'image' => '',
    'alt' => '',
    'reverse' => false,
    'tone' => 'light',
])

@php
    $isDark = $tone === 'navy';
@endphp

<section {{ $attributes->class(['story-band', 'is-reverse' => $reverse, 'is-navy' => $isDark]) }}>
    <div class="story-band__media">
        <img
            src="{{ $image }}"
            alt="{{ $alt }}"
            width="1200"
            height="900"
            loading="lazy"
            decoding="async"
        >
    </div>
    <div class="story-band__copy">
        @if ($kicker)
            <p class="kicker">{{ $kicker }}</p>
        @endif
        <h2>{{ $title }}</h2>
        <p>{{ $description }}</p>
        <a href="{{ $ctaHref }}" class="{{ $isDark ? 'btn-primary' : 'btn-secondary' }}">{{ $ctaLabel }}</a>
    </div>
</section>
