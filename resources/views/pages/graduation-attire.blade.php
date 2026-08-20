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

    <section id="shop" class="container-shell section-lg scroll-mt-24">
        <x-ui.section-header
            kicker="Graduation Attire"
            title="Graduation Wear in Kenya"
            description="Premium gowns, caps, hoods, stoles, and complete sets for hire and sale."
        />

        <div class="luxury-grid mt-8 md:grid-cols-3">
            @foreach ($properties as $property)
                <x-ui.property-card :property="$property" />
            @endforeach
        </div>

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
