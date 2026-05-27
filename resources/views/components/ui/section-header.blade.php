@props([
    'kicker' => null,
    'title' => '',
    'description' => null,
    'align' => 'left',
])

@php
    $alignClass = $align === 'center' ? 'mx-auto text-center' : '';
@endphp

<div class="max-w-3xl {{ $alignClass }}">
    @if ($kicker)
        <p class="kicker">{{ $kicker }}</p>
    @endif

    <h2 class="mt-3 font-semibold">{{ $title }}</h2>

    @if ($description)
        <p class="mt-4 text-zinc-600">{{ $description }}</p>
    @endif
</div>
