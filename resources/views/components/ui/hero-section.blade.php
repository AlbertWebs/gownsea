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
    <div class="luxury-grid items-start md:grid-cols-2 md:gap-10">
        <div class="max-w-3xl">
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

            <div
                class="surface relative overflow-hidden"
                x-data="{ active: 0, total: {{ count($normalizedSlides) }}, timer: null }"
                x-init="if (total > 1) { timer = setInterval(() => { active = (active + 1) % total }, 5500) }"
                @mouseenter="if (timer) { clearInterval(timer); timer = null }"
                @mouseleave="if (!timer && total > 1) { timer = setInterval(() => { active = (active + 1) % total }, 5500) }"
            >
                <div class="relative aspect-[16/9]">
                    @foreach ($normalizedSlides as $idx => $slide)
                        <div
                            x-show="active === {{ $idx }}"
                            x-transition:enter="transition ease-out duration-700"
                            x-transition:enter-start="opacity-0 scale-105"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-500"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute inset-0"
                        >
                            <img
                                src="{{ $slide['src'] }}"
                                alt="{{ $slide['headline'] }}"
                                loading="{{ $idx === 0 ? 'eager' : 'lazy' }}"
                                decoding="async"
                                width="1400"
                                height="788"
                                class="h-full w-full object-cover transition-transform duration-[5500ms] ease-out"
                                :class="{ 'scale-110': active === {{ $idx }} }"
                                x-on:error="$el.src='https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1400&q=70'"
                            >

                            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/55 via-zinc-900/20 to-transparent"></div>

                            <div
                                class="absolute bottom-4 left-4 right-4 md:bottom-6 md:left-6 md:right-6"
                                x-transition:enter="transition ease-out duration-700 delay-150"
                                x-transition:enter-start="opacity-0 translate-y-3"
                                x-transition:enter-end="opacity-100 translate-y-0"
                            >
                                <div class="inline-flex items-center rounded-full border border-white/35 bg-white/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-white backdrop-blur">
                                    {{ $slide['label'] }}
                                </div>
                                <p class="mt-3 max-w-lg text-sm font-medium text-white/95 md:text-base">{{ $slide['headline'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (count($normalizedSlides) > 1)
                    <button
                        type="button"
                        class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full border border-white/25 bg-white/20 px-3 py-2 text-xs font-semibold text-white backdrop-blur transition hover:bg-white/30"
                        @click="active = (active - 1 + total) % total"
                        aria-label="Previous slide"
                    >
                        ‹
                    </button>
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full border border-white/25 bg-white/20 px-3 py-2 text-xs font-semibold text-white backdrop-blur transition hover:bg-white/30"
                        @click="active = (active + 1) % total"
                        aria-label="Next slide"
                    >
                        ›
                    </button>

                    <div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 items-center gap-2 rounded-full border border-white/20 bg-zinc-900/45 px-3 py-2 backdrop-blur">
                        @foreach ($normalizedSlides as $idx => $slide)
                            <button
                                type="button"
                                class="h-2 w-2 rounded-full bg-white/45 transition-all"
                                :class="{ 'w-5 bg-white': active === {{ $idx }} }"
                                @click="active = {{ $idx }}"
                                aria-label="Go to slide {{ $idx + 1 }}"
                            ></button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
