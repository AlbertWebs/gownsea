@props([
    'title' => '',
    'description' => '',
    'primaryLabel' => '',
    'primaryHref' => '#',
    'secondaryLabel' => null,
    'secondaryHref' => null,
])

<section class="container-shell section-md">
    <div class="surface-muted px-6 py-8 md:px-10 md:py-10">
        <div class="luxury-grid items-center md:grid-cols-[1fr_auto]">
            <div>
                <h3 class="font-semibold">{{ $title }}</h3>
                <p class="mt-3 text-sm text-zinc-600">{{ $description }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ $primaryHref }}" class="btn-primary">{{ $primaryLabel }}</a>
                @if ($secondaryLabel && $secondaryHref)
                    <a href="{{ $secondaryHref }}" class="btn-secondary">{{ $secondaryLabel }}</a>
                @endif
            </div>
        </div>
    </div>
</section>
