@extends('layouts.public')

@push('json_ld')
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endpush

@section('content')
    <x-ui.page-banner
        title="Church Wear"
        subtitle="Church garments and coordinated choral attire"
        ctaLabel="Shop Now"
        ctaHref="#shop"
        image="{{ $bannerImage }}"
        alt="Church and choral attire for clergy, choirs and congregations in Kenya"
    />

    <section id="shop" class="bg-zinc-50 section-lg scroll-mt-24">
        <div class="container-shell">
            <div class="featured-rail__intro">
                <p class="kicker">Church Wear</p>
                <h2 class="featured-rail__heading mt-3 font-semibold">{{ $categoryHeading }}</h2>
                <p class="featured-rail__lede mt-4 text-zinc-600">{{ $categoryIntro }}</p>
            </div>

            <div class="luxury-grid mt-8 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($properties as $property)
                    <x-ui.product-tile :property="$property" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="container-shell section-md">
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
    </section>

    @include('partials.clients')

    @if (! empty($faqs))
        <section class="container-shell section-md">
            <div>
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
        </section>
    @endif

    <x-ui.cta-band
        title="Looking for church or choir attire?"
        description="Tell us your congregation size, colours, and timeline and we will recommend the right set."
        primaryLabel="Talk to Team"
        :primaryHref="route('contact-us')"
        secondaryLabel="Shop Collection"
        :secondaryHref="route('shop-attire.collection', 'church-wear')"
    />
@endsection
