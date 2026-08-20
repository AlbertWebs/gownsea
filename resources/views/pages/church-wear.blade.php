@extends('layouts.public')

@section('content')
    <x-ui.page-banner
        title="Church Wear"
        subtitle="Quality church and choral attire"
        ctaLabel="Shop Now"
        ctaHref="#shop"
        image="{{ $bannerImage }}"
        alt="Church and choral wear for congregations in Kenya"
    />

    <section id="shop" class="container-shell section-lg scroll-mt-24">
        <x-ui.section-header
            kicker="Church Wear"
            title="Church and Choral Wear in Kenya"
            description="Clergy robes, cassocks, choir gowns, and liturgical accessories for hire and sale."
        />

        <div class="luxury-grid mt-8 md:grid-cols-3">
            @foreach ($properties as $property)
                <x-ui.property-card :property="$property" />
            @endforeach
        </div>

        <div class="luxury-grid mt-10 md:grid-cols-3">
            <article class="surface p-6">
                <h3 class="font-semibold">Bespoke choir wear</h3>
                <p class="mt-3 text-sm text-zinc-600">Personalise church and choir garments to suit any congregation.</p>
            </article>
            <article class="surface p-6">
                <h3 class="font-semibold">Free delivery around Nairobi</h3>
                <p class="mt-3 text-sm text-zinc-600">Convenient delivery throughout Nairobi and surrounding areas.</p>
            </article>
            <article class="surface p-6">
                <h3 class="font-semibold">Sustainable focus</h3>
                <p class="mt-3 text-sm text-zinc-600">Durable ceremonial garments made for repeated sacred occasions.</p>
            </article>
        </div>

        @include('partials.clients')

        @if (! empty($faqs))
            <div class="mt-12">
                <x-ui.section-header kicker="FAQs" title="Common questions" />
                <div class="mt-8 grid gap-4 md:grid-cols-2">
                    @foreach ($faqs as $question => $answer)
                        <article class="surface p-6">
                            <h3 class="text-base font-semibold">{{ $question }}</h3>
                            <p class="mt-3 text-sm text-zinc-600">{{ $answer }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    <x-ui.cta-band
        title="Looking for church or choir attire?"
        description="Tell us your congregation size, colours, and timeline and we will recommend the right set."
        primaryLabel="Talk to Team"
        :primaryHref="route('contact-us')"
        secondaryLabel="Shop Collection"
        :secondaryHref="route('shop-attire.collection', 'church-wear')"
    />
@endsection
