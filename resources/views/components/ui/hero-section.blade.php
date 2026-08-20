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

<section class="hero-section container-shell">
    <div class="flex flex-col gap-6 md:flex-row md:items-center md:gap-8">
        <div class="flex max-w-3xl flex-col justify-center md:w-1/2">
            @if ($kicker)
                <p class="kicker text-[#d42127]">{{ $kicker }}</p>
            @endif

            <h1 class="hero-title mt-3 font-bold">{{ $title }}</h1>

            @if ($description)
                <p class="mt-4 max-w-2xl text-lg text-zinc-600">{{ $description }}</p>
            @endif

            @if ($primaryLabel || $secondaryLabel)
                <div class="mt-6 flex flex-wrap gap-3">
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

            <div class="w-full md:w-1/2">
            <div
                class="hero-carousel surface relative w-full overflow-hidden"
                role="region"
                aria-roledescription="carousel"
                aria-label="Featured collections"
                tabindex="0"
                x-data="{
                    active: 0,
                    leaving: null,
                    dir: 1,
                    total: {{ count($normalizedSlides) }},
                    duration: 6000,
                    progress: 0,
                    paused: false,
                    timer: null,
                    leaveTimer: null,
                    start() {
                        if (this.total < 2) return;
                        this.paused = false;
                        if (this.timer) return;
                        const step = 50;
                        this.timer = setInterval(() => {
                            if (this.paused) return;
                            this.progress += (step / this.duration) * 100;
                            if (this.progress >= 100) {
                                this.show((this.active + 1) % this.total);
                            }
                        }, step);
                    },
                    stop() { this.paused = true },
                    show(index) {
                        if (index === this.active) return;
                        const from = this.active;
                        let dir = index > from ? 1 : -1;
                        if (from === this.total - 1 && index === 0) dir = 1;
                        if (from === 0 && index === this.total - 1) dir = -1;
                        this.dir = dir;
                        this.leaving = from;
                        this.active = index;
                        this.progress = 0;
                        clearTimeout(this.leaveTimer);
                        this.leaveTimer = setTimeout(() => { this.leaving = null }, 900);
                    },
                    next() { this.show((this.active + 1) % this.total) },
                    prev() { this.show((this.active - 1 + this.total) % this.total) },
                    go(index) { this.show(index) }
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
                            class="hero-carousel__slide"
                            :class="{
                                'is-active': active === {{ $idx }},
                                'is-leaving': leaving === {{ $idx }},
                                'is-forward': dir === 1,
                                'is-backward': dir === -1
                            }"
                            role="group"
                            aria-roledescription="slide"
                            aria-label="Slide {{ $idx + 1 }} of {{ count($normalizedSlides) }}"
                            :aria-hidden="active === {{ $idx }} ? 'false' : 'true'"
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

                            <div class="hero-carousel__overlay pointer-events-none absolute inset-0 bg-gradient-to-t from-zinc-950/70 via-zinc-950/20 to-transparent"></div>

                            <div class="hero-carousel__caption absolute inset-x-4 bottom-16 z-10 md:inset-x-6 md:bottom-[4.75rem]">
                                <span class="hero-carousel__tag inline-flex items-center rounded-2xl border border-white/30 bg-white/15 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-white backdrop-blur-md">
                                    {{ $slide['label'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if (count($normalizedSlides) > 1)
                    <div
                        class="hero-carousel__progress"
                        role="progressbar"
                        aria-label="Slide timer"
                        :aria-valuenow="Math.round(progress)"
                        aria-valuemin="0"
                        aria-valuemax="100"
                    >
                        <div class="hero-carousel__progress-bar" :style="`width: ${progress}%`"></div>
                    </div>

                    <div class="hero-carousel__dots absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 items-center gap-1.5 rounded-2xl bg-zinc-950/55 px-2.5 py-1.5 backdrop-blur-md">
                        @foreach ($normalizedSlides as $idx => $slide)
                            <button
                                type="button"
                                class="hero-carousel__dot h-2.5 w-2.5 rounded-full bg-white/40"
                                :class="{ 'is-current': active === {{ $idx }} }"
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
