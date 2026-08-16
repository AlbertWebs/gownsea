@props([
    'href' => null,
    'variant' => 'primary',
    'icon' => null,
    'type' => 'submit',
])

@php
    $variantClass = match ($variant) {
        'secondary' => 'btn-secondary',
        'ghost' => 'btn-ghost',
        'danger' => 'btn-danger',
        'navy' => 'btn-navy',
        'teal' => 'btn-teal',
        'violet' => 'btn-violet',
        default => 'btn-primary',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($variantClass) }}>
        @if ($icon)<x-admin.icon :name="$icon" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($variantClass) }}>
        @if ($icon)<x-admin.icon :name="$icon" />@endif
        {{ $slot }}
    </button>
@endif
