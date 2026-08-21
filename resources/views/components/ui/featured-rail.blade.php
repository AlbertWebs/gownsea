@props([
    'items' => [],
    'kicker' => 'Featured',
    'title' => 'Browse our top ceremonial collections',
    'description' => null,
])

@php
    $slides = collect($items)->values()->map(function ($property) {
        $slug = $property['slug'] ?? 'product';
        $image = $property['image'] ?? '/images/site/hero.webp';
        $gallery = $property['gallery'] ?? [];
        $thumb = $gallery[0] ?? $image;

        return [
            'title' => $property['title'] ?? 'Featured attire',
            'price' => $property['price'] ?? 'Request quote',
            'image' => $image,
            'thumb' => $thumb,
            'href' => $property['url'] ?? route('products.show', $slug),
            'quote' => route('products.show', $slug).'#request-quote',
        ];
    })->all();
@endphp

<section class="featured-rail section-lg">
    <div class="container-shell">
        <div class="featured-rail__intro">
            @if ($kicker)
                <p class="kicker">{{ $kicker }}</p>
            @endif
            <h2 class="featured-rail__heading mt-3 font-semibold">{{ $title }}</h2>
            @if ($description)
                <p class="featured-rail__lede mt-4 text-zinc-600">{{ $description }}</p>
            @endif
        </div>

        <div
            class="featured-rail__shell mt-8"
            x-data="{
                canPrev: false,
                canNext: true,
                sync() {
                    const track = this.$refs.track;
                    if (! track) return;
                    const max = track.scrollWidth - track.clientWidth - 2;
                    this.canPrev = track.scrollLeft > 2;
                    this.canNext = track.scrollLeft < max;
                },
                step(dir) {
                    const track = this.$refs.track;
                    if (! track) return;
                    const card = track.querySelector('.featured-rail__card');
                    const amount = card ? card.getBoundingClientRect().width + 16 : track.clientWidth * 0.8;
                    track.scrollBy({ left: dir * amount, behavior: 'smooth' });
                }
            }"
            x-init="
                $nextTick(() => sync());
                $refs.track?.addEventListener('scroll', () => sync(), { passive: true });
                window.addEventListener('resize', () => sync());
            "
        >
            <div class="featured-rail__track" x-ref="track" tabindex="0" aria-label="Featured collections">
                @foreach ($slides as $slide)
                    <article class="featured-rail__card">
                        <a href="{{ $slide['href'] }}" class="featured-rail__media" aria-label="{{ $slide['title'] }}">
                            <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" loading="lazy" decoding="async">
                        </a>

                        <div class="featured-rail__bar">
                            <a href="{{ $slide['href'] }}" class="featured-rail__thumb" tabindex="-1" aria-hidden="true">
                                <img src="{{ $slide['thumb'] }}" alt="" loading="lazy" decoding="async">
                            </a>

                            <div class="featured-rail__copy">
                                <a href="{{ $slide['href'] }}" class="featured-rail__name">{{ $slide['title'] }}</a>
                                <p class="featured-rail__price">{{ $slide['price'] }}</p>
                            </div>

                            <a
                                href="{{ $slide['quote'] }}"
                                class="featured-rail__add"
                                aria-label="Request quote for {{ $slide['title'] }}"
                                title="Request quote"
                            >
                                <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M8 3.2v9.6M3.2 8h9.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="featured-rail__nav" aria-hidden="false">
                <button
                    type="button"
                    class="featured-rail__arrow"
                    @click="step(-1)"
                    :disabled="!canPrev"
                    aria-label="Previous featured items"
                >
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M10 3.5 5.5 8 10 12.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button
                    type="button"
                    class="featured-rail__arrow"
                    @click="step(1)"
                    :disabled="!canNext"
                    aria-label="Next featured items"
                >
                    <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M6 3.5 10.5 8 6 12.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>
