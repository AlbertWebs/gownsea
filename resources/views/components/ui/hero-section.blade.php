@props([
    'kicker' => null,
    'title' => '',
    'description' => null,
    'primaryLabel' => null,
    'primaryHref' => null,
    'secondaryLabel' => null,
    'secondaryHref' => null,
    'image' => null,
    'images' => [],
])

<section class="container-shell section-lg">
    <div class="flex flex-col gap-8 md:flex-row md:items-stretch md:gap-10">
        <div class="flex max-w-3xl flex-col justify-center md:w-1/2">
            @if ($kicker)
                <p class="kicker">{{ $kicker }}</p>
            @endif

            <h1 class="mt-4 font-semibold">{{ $title }}</h1>

            @if ($description)
                <p class="mt-6 max-w-2xl text-lg text-zinc-600">{{ $description }}</p>
            @endif

            @if ($primaryLabel || $secondaryLabel)
                <div class="mt-8 flex flex-wrap gap-3">
                    @if ($primaryLabel && $primaryHref)
                        <a href="{{ $primaryHref }}" class="btn-primary">{{ $primaryLabel }}</a>
                    @endif
                    @if ($secondaryLabel && $secondaryHref)
                        <a href="{{ $secondaryHref }}" class="btn-secondary">{{ $secondaryLabel }}</a>
                    @endif
                </div>
            @endif
        </div>

        @php
            $heroImages = count($images) ? $images : ($image ? [$image] : []);
        @endphp

        @if (count($heroImages))
            @php
                $normalizedSlides = collect($heroImages)->map(function ($item, $index) use ($title) {
                    if (is_array($item)) {
                        return [
                            'src' => $item['src'] ?? '',
                            'label' => $item['label'] ?? 'Featured',
                            'headline' => $item['headline'] ?? $title,
                        ];
                    }

                    return [
                        'src' => $item,
                        'label' => 'Featured',
                        'headline' => $title,
                    ];
                })->values()->all();
            @endphp

            <div class="relative h-80 w-full md:h-auto md:w-1/2">
            <div
                class="hero-carousel surface absolute inset-0 overflow-hidden"
                role="region"
                aria-roledescription="carousel"
                aria-label="Featured collections"
                tabindex="0"
                x-data="{
                    active: 0,
                    total: {{ count($normalizedSlides) }},
                    timer: null,
                    start() { if (this.total > 1) { this.timer = setInterval(() => this.next(), 6000) } },
                    stop() { if (this.timer) { clearInterval(this.timer); this.timer = null } },
                    next() { this.active = (this.active + 1) % this.total },
                    prev() { this.active = (this.active - 1 + this.total) % this.total },
                    go(index) { this.active = index }
                }"
                x-init="start()"
                @mouseenter="stop()"
                @mouseleave="start()"
                @focusin="stop()"
                @focusout="start()"
                @keydown.arrow-right.prevent="next()"
                @keydown.arrow-left.prevent="prev()"
            >
                <div class="absolute inset-0">
                    @foreach ($normalizedSlides as $idx => $slide)
                        <div
                            x-show="active === {{ $idx }}"
                            @if ($idx !== 0) x-cloak @endif
                            x-transition:enter="transition ease-out duration-700"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-500"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute inset-0"
                            role="group"
                            aria-roledescription="slide"
                            aria-label="Slide {{ $idx + 1 }} of {{ count($normalizedSlides) }}"
                        >
                            <img
                                src="{{ $slide['src'] }}"
                                alt="{{ $slide['headline'] }}"
                                loading="{{ $idx === 0 ? 'eager' : 'lazy' }}"
                                decoding="async"
                                width="1400"
                                height="788"
                                class="hero-carousel__image h-full w-full object-cover"
                                x-on:error="$el.src='/images/site/hero.webp'"
                            >

                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-zinc-950/70 via-zinc-950/20 to-transparent"></div>

                            <div class="absolute inset-x-4 bottom-16 z-10 md:inset-x-6 md:bottom-[4.75rem]">
                                <span class="inline-flex items-center rounded-full border border-white/30 bg-white/15 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-white backdrop-blur-md">
                                    {{ $slide['label'] }}
                                </span>
                                <p class="mt-2 max-w-md text-sm font-medium leading-snug text-white drop-shadow-sm md:text-base">{{ $slide['headline'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (count($normalizedSlides) > 1)
                    <button
                        type="button"
                        class="hero-carousel__nav left-3"
                        @click="prev()"
                        aria-label="Previous slide"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M10 3.5 5.5 8 10 12.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="hero-carousel__nav right-3"
                        @click="next()"
                        aria-label="Next slide"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M6 3.5 10.5 8 6 12.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <div class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 items-center gap-1.5 rounded-full bg-zinc-950/55 px-2.5 py-1.5 backdrop-blur-md">
                        @foreach ($normalizedSlides as $idx => $slide)
                            <button
                                type="button"
                                class="h-2.5 w-2.5 rounded-full bg-white/40 transition-all duration-300 hover:bg-white/80"
                                :class="{ 'w-6 bg-white': active === {{ $idx }} }"
                                @click="go({{ $idx }})"
                                :aria-current="active === {{ $idx }} ? 'true' : 'false'"
                                aria-label="Go to {{ $slide['label'] }}"
                            ></button>
                        @endforeach
                    </div>
                @endif
            </div>
            </div>
        @endif
    </div>
</section>
