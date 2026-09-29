@extends('layouts.public')

@section('content')
    <x-ui.page-banner
        title="Graduation Attire"
        subtitle="University-standard graduation attire"
        ctaLabel="Shop Now"
        ctaHref="#shop"
        image="{{ $bannerImage }}"
        alt="University-standard graduation gowns, caps, and hoods"
    />

    <section id="shop" class="bg-zinc-50 section-lg scroll-mt-24">
        <div class="container-shell">
            <div class="featured-rail__intro">
                <p class="kicker">Graduation Attire</p>
                <h2 class="featured-rail__heading mt-3 font-semibold">Graduation Wear in Kenya</h2>
                <p class="featured-rail__lede mt-4 text-zinc-600">Premium gowns, caps, hoods, stoles, and complete sets for hire and sale.</p>
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
