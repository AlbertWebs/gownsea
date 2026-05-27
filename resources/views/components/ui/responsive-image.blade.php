@props([
    'src' => '',
    'alt' => '',
    'ratio' => '4:3',
    'class' => '',
    'imgClass' => '',
    'priority' => false,
])

@php
    $ratioClass = match ($ratio) {
        '16:9' => 'aspect-[16/9]',
        '3:2' => 'aspect-[3/2]',
        default => 'aspect-[4/3]',
    };
@endphp

<div
    x-data="{ loaded: false, failed: false }"
    x-init="
        if ($refs.image && $refs.image.complete) {
            loaded = true;
            failed = $refs.image.naturalWidth === 0;
        }
    "
    {{ $attributes->merge(['class' => "relative overflow-hidden {$ratioClass} {$class}"]) }}
>
    <div
        x-cloak
        x-show="!loaded"
        x-transition.opacity
        class="absolute inset-0 animate-pulse bg-zinc-200/70"
        aria-hidden="true"
    ></div>

    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        loading="{{ $priority ? 'eager' : 'lazy' }}"
        decoding="async"
        width="1200"
        height="900"
        class="h-full w-full object-cover transition duration-500 group-hover:scale-105 {{ $imgClass }}"
        x-ref="image"
        x-on:load="loaded = true"
        x-on:error="failed = true; loaded = true; $el.src='https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=70';"
    >

    <div
        x-cloak
        x-show="failed"
        class="absolute bottom-2 right-2 rounded bg-zinc-900/75 px-2 py-1 text-[10px] font-medium text-white"
    >
        fallback image
    </div>
</div>
