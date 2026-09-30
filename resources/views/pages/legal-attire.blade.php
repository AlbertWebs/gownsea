@extends('layouts.public')

@push('json_ld')
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endpush

@section('content')
    <x-ui.page-banner
        title="Legal Attire"
        subtitle="Court attire and professional legal garments"
        ctaLabel="Shop Now"
        ctaHref="#shop"
        image="{{ $bannerImage }}"
        alt="Legal attire including advocate gowns and barrister dress in Kenya"
    />

    <section id="shop" class="bg-zinc-50 section-lg scroll-mt-24">
        <div class="container-shell">
            <div class="featured-rail__intro">
                <p class="kicker">Legal Attire</p>
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

    <section class="container-shell pb-8" aria-label="More legal attire information">
        <p class="max-w-4xl text-sm leading-7 text-zinc-600">Review the listed pieces and enquire with the garment, quantity and sizing details you need. For a coordinated institutional order, <a class="font-semibold text-zinc-900 underline" href="{{ route('bulk-inquiry') }}">request a bulk quote</a>. You can also browse <a class="font-semibold text-zinc-900 underline" href="{{ route('graduation-attire') }}">graduation attire</a> and <a class="font-semibold text-zinc-900 underline" href="{{ route('church-wear') }}">church wear</a>.</p>
    </section>

    <section class="container-shell section-md">
        <div class="luxury-grid md:grid-cols-3">
            <article class="surface p-6">
                <h3 class="font-semibold">Tailored to perfection</h3>
                <p class="mt-3 text-sm text-zinc-600">Expertly crafted legal attire built for comfort and courtroom presence.</p>
            </article>
            <article class="surface p-6">
                <h3 class="font-semibold">Free delivery around Nairobi</h3>
                <p class="mt-3 text-sm text-zinc-600">Convenient delivery in Nairobi and surrounding areas for legal professionals.</p>
            </article>
            <article class="surface p-6">
                <h3 class="font-semibold">Sustainable focus</h3>
                <p class="mt-3 text-sm text-zinc-600">Quality materials and durable products to reduce replacement cycles.</p>
            </article>
        </div>
    </section>

    @include('partials.clients')

    <x-ui.cta-band
        title="Need legal attire tailored for your practice?"
        description="Get advisory support on sizing, package options, and fast Nairobi-area delivery."
        primaryLabel="Talk to Team"
        :primaryHref="route('contact-us')"
    />
@endsection
