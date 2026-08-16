@props(['name' => 'plus'])

@php
    $paths = [
        'plus' => '<path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'save' => '<path d="M5 5h11l3 3v11H5V5z" stroke="currentColor" stroke-width="1.8"/><path d="M8 5v4h7V5M8 19v-6h8v6" stroke="currentColor" stroke-width="1.8"/>',
        'edit' => '<path d="M4 20h4l10-10-4-4L4 16v4zM14 6l4 4" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
        'trash' => '<path d="M5 7h14M10 7V5h4v2M8 7l1 12h6l1-12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
        'filter' => '<path d="M4 6h16l-6 7v5l-4-2v-3L4 6z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>',
        'x' => '<path d="M7 7l10 10M17 7 7 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'download' => '<path d="M12 4v10m0 0-4-4m4 4 4-4M5 18h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
        'copy' => '<rect x="8" y="8" width="11" height="11" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M5 15V6a2 2 0 0 1 2-2h9" stroke="currentColor" stroke-width="1.8"/>',
        'back' => '<path d="M14 6 8 12l6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
        'kanban' => '<path d="M5 5h4v14H5zM10.5 5h4v9h-4zM16 5h3v6h-3z" stroke="currentColor" stroke-width="1.6"/>',
        'note' => '<path d="M7 5h10v14H7z" stroke="currentColor" stroke-width="1.8"/><path d="M10 9h4M10 13h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
        'check' => '<path d="M5 12.5 9.5 17 19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
        'user' => '<circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/><path d="M5 19c1.2-3 3.4-4.5 7-4.5S17.8 16 19 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
        'list' => '<path d="M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>',
        'convert' => '<path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>',
        'eye' => '<path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.4" stroke="currentColor" stroke-width="1.8"/>',
    ];
@endphp

<svg {{ $attributes->class('h-4 w-4 shrink-0') }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
    {!! $paths[$name] ?? $paths['plus'] !!}
</svg>
