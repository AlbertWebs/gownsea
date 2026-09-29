@extends('layouts.public')

@section('content')
    <x-ui.page-banner
        title="Legal Attire"
        subtitle="Courtroom-standard legal attire"
        ctaLabel="Shop Now"
        ctaHref="#shop"
        image="{{ $bannerImage }}"
        alt="Premium legal attire for advocates and barristers"
    />

    <section id="shop" class="bg-zinc-50 section-lg scroll-mt-24">
        <div class="container-shell">
            <div class="featured-rail__intro">
                <p class="kicker">Legal Attire</p>
                <h2 class="featured-rail__heading mt-3 font-semibold">Legal Wear in Kenya</h2>
                <p class="featured-rail__lede mt-4 text-zinc-600">Premium barrister wigs, gowns, bibs, and advocates shirts for hire and sale.</p>
            </div>

            <div class="luxury-grid mt-8 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($properties as $property)
                    <x-ui.product-tile :property="$property" />
                @endforeach
            </div>
        </div>

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
