@extends('layouts.public')

@push('json_ld')
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endpush

@section('content')
    <x-ui.page-banner
        title="Graduation Attire"
        subtitle="Graduation gowns, academic sets and accessories"
        ctaLabel="Shop Now"
        ctaHref="#shop"
        image="{{ $bannerImage }}"
        alt="Graduation gowns, caps and academic hoods in Kenya"
    />

    <section id="shop" class="bg-zinc-50 section-lg scroll-mt-24">
        <div class="container-shell">
            <div class="featured-rail__intro">
                <p class="kicker">Graduation Attire</p>
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

    <section class="container-shell pb-8" aria-label="More graduation attire information">
        <p class="max-w-4xl text-sm leading-7 text-zinc-600">Choose individual graduation accessories or explore complete sets for your ceremony. For help with an order, <a class="font-semibold text-zinc-900 underline" href="{{ route('gown-for-hire') }}">learn about graduation gown hire</a>, request a <a class="font-semibold text-zinc-900 underline" href="{{ route('bulk-inquiry') }}">bulk graduation attire quote</a>, or browse our <a class="font-semibold text-zinc-900 underline" href="{{ route('legal-attire') }}">legal attire</a> and <a class="font-semibold text-zinc-900 underline" href="{{ route('church-wear') }}">church wear</a>.</p>
    </section>

    <section class="container-shell section-md">

        <div class="luxury-grid mt-10 md:grid-cols-3">
            <article class="surface p-6">
                <h3 class="font-semibold">Wide selection</h3>
                <p class="mt-3 text-sm text-zinc-600">Gowns for preschool through PhD, with academic colours and reliable sizing.</p>
            </article>
            <article class="surface p-6">
                <h3 class="font-semibold">Hire or purchase</h3>
                <p class="mt-3 text-sm text-zinc-600">Flexible packages for individuals and institutions, with Nairobi-area delivery.</p>
            </article>
            <article class="surface p-6">
                <h3 class="font-semibold">Ceremony-ready support</h3>
                <p class="mt-3 text-sm text-zinc-600">Guidance on sets, accessories, and bulk planning so graduation day stays smooth.</p>
            </article>
        </div>
    </section>

    <x-ui.cta-band
        title="Need graduation attire for your ceremony?"
        description="Hire a gown now or request a bulk quote for your institution."
        primaryLabel="Hire a Gown"
        :primaryHref="route('gown-for-hire')"
        secondaryLabel="Bulk Inquiry"
        :secondaryHref="route('bulk-inquiry')"
    />
@endsection
