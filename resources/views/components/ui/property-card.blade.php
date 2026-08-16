@props([
    'property',
    'showQuickView' => false,
])

<article class="group surface overflow-hidden">
    <x-ui.responsive-image
        :src="$property['image'] ?? 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=70'"
        :alt="$property['title']"
        ratio="4:3"
    />

    <div class="p-5">
        <h3 class="text-lg font-semibold">{{ $property['title'] }}</h3>
        <p class="mt-2 text-sm text-zinc-600">{{ $property['description'] }}</p>

        <div class="mt-4 flex items-center justify-between text-sm">
            <span class="font-semibold text-[#d42127]">{{ $property['price'] }}</span>
            <span class="text-zinc-500">{{ $property['location'] }}</span>
        </div>

        <div class="mt-4 flex items-center gap-4">
            <a href="{{ route('products.show', $property['slug'] ?? 'graduation-attire') }}" class="text-sm font-semibold text-zinc-900 underline">
                View details
            </a>

            @if ($showQuickView)
                <button
                    type="button"
                    class="text-sm font-semibold text-zinc-700 underline"
                    @click="$dispatch('quick-view-open', { property: @js($property) })"
                >
                    Quick view
                </button>
            @endif
        </div>
    </div>
</article>
