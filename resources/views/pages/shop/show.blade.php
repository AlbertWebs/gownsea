@extends('layouts.public')

@section('content')
    <section class="container-shell section-lg">
        <x-ui.section-header
            :kicker="$subheading"
            :title="$heading"
            description="Discover premium regalia for hire and sale. Explore options, request quotes, and get support from our team."
        />

        <div class="luxury-grid mt-10 md:grid-cols-2 lg:grid-cols-4">
            @forelse ($items as $property)
                <x-ui.product-tile :property="$property" />
            @empty
                <div class="surface border-t-4 border-t-[#0f2744] p-6 md:col-span-2 lg:col-span-4">
                    <p class="kicker">Collection</p>
                    <p class="mt-2 text-sm text-zinc-600">Collection details are being curated. Contact us for immediate guidance and pricing.</p>
                </div>
            @endforelse
        </div>
    </section>

    <x-ui.cta-band
        title="Need a fast quote?"
        description="Share your category, quantity, and timeline and we'll respond quickly."
        primaryLabel="Request Quote"
        :primaryHref="route('contact-us')"
        secondaryLabel="Bulk Inquiry"
        :secondaryHref="route('bulk-inquiry')"
    />
@endsection
