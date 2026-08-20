@props([
    'property',
    'showQuickView' => false,
    'ratio' => '4:5',
])

@php
    $slug = $property['slug'] ?? 'graduation-attire';
    $detailHref = $property['url'] ?? route('products.show', $slug);
    $price = (string) ($property['price'] ?? '');
    $isQuotePrice = str_contains(mb_strtolower($price), 'quote');
    $quoteHref = route('products.show', $slug).'#request-quote';
    $category = (string) ($property['category'] ?? '');
    $categoryLabel = match ($category) {
        'legal' => 'Legal Wear',
        'church' => 'Church Wear',
        'graduation' => 'Graduation',
        default => $category ? str_replace('-', ' ', $category) : null,
    };
    $isHire = (bool) ($property['is_hire'] ?? false) || str_contains(mb_strtolower((string) ($property['cta'] ?? '')), 'hire');
    $galleryCount = count($property['gallery'] ?? []);
@endphp

<article {{ $attributes->class('product-card group') }}>
    <a href="{{ $detailHref }}" class="product-card__media block" aria-label="View {{ $property['title'] }}">
        @if ($categoryLabel)
            <span class="product-card__badge">{{ $categoryLabel }}</span>
        @endif

        @if ($isHire)
            <span class="product-card__chip">Hire available</span>
        @elseif ($galleryCount > 1)
            <span class="product-card__chip">{{ $galleryCount }} photos</span>
        @endif

        <x-ui.responsive-image
            :src="$property['image'] ?? 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=70'"
            :alt="$property['title']"
            :ratio="$ratio"
            fit="contain"
        />

        <span class="product-card__peek">
            <span>View product</span>
        </span>
    </a>

    <div class="product-card__body">
        <h3 class="product-card__title">
            <a href="{{ $detailHref }}" class="transition-colors hover:text-[#d42127]">{{ $property['title'] }}</a>
        </h3>

        <div class="product-card__meta">
            @if ($isQuotePrice)
                <a href="{{ $quoteHref }}" class="product-card__price is-quote">{{ $price }}</a>
            @else
                <p class="product-card__price">{{ $price }}</p>
            @endif
            <p class="product-card__location">
                <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M8 14s5-3.6 5-7.2A5 5 0 0 0 3 6.8C3 10.4 8 14 8 14Z" stroke="currentColor" stroke-width="1.4"/>
                    <circle cx="8" cy="6.8" r="1.4" fill="currentColor"/>
                </svg>
                {{ $property['location'] }}
            </p>
        </div>

        <div class="product-card__perks">
            <span class="product-card__perk">Hire &amp; sale</span>
            <span class="product-card__perk">Nairobi delivery</span>
            <span class="product-card__perk">Custom fit</span>
        </div>

        <div class="product-card__actions">
            <a href="{{ $detailHref }}" class="btn-secondary btn-sm">View Details</a>
            <a href="{{ $quoteHref }}" class="btn-primary btn-sm">Request Quote</a>
        </div>

        @if ($showQuickView)
            <button
                type="button"
                class="mt-2 text-xs font-semibold text-zinc-600 transition-colors hover:text-[#d42127]"
                @click="$dispatch('quick-view-open', { property: @js($property) })"
            >
                Quick view
            </button>
        @endif
    </div>
</article>
