@extends('layouts.public')

@section('content')
    <section class="container-shell section-lg">
        <div class="luxury-grid md:grid-cols-[1.1fr_.9fr] md:items-start">
            <div>
                <p class="kicker">Collection detail</p>
                <h1 class="mt-3 font-semibold">{{ $property['title'] }}</h1>
                <p class="mt-6 text-lg text-zinc-600">{{ $property['description'] }}</p>

                <div class="surface-muted mt-8 grid gap-4 p-6 md:grid-cols-3">
                    <div>
                        <p class="text-xs text-zinc-500">Price</p>
                        <p class="font-semibold text-[#d42127]">{{ $property['price'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Location</p>
                        <p class="font-semibold">{{ $property['location'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Action</p>
                        <a href="{{ route('contact-us') }}" class="font-semibold underline">{{ $property['cta'] }}</a>
                    </div>
                </div>
            </div>

            <x-ui.responsive-image
                :src="$property['image'] ?? 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=70'"
                :alt="$property['title']"
                ratio="3:2"
                class="surface"
            />
        </div>
    </section>

    <x-ui.cta-band
        title="Need this package for your event?"
        description="Talk to our support team for quote guidance, fitting options, and delivery timelines."
        primaryLabel="Contact Support"
        :primaryHref="route('contact-us')"
        secondaryLabel="Back to Shop"
        :secondaryHref="route('shop-attire.collection', 'graduation-attire')"
    />
@endsection
