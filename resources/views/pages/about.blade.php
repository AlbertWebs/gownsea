@extends('layouts.public')

@section('content')
    <section class="container-shell section-lg">
        <x-ui.section-header
            kicker="Who we are"
            title="About Gownsea"
            description="Gownsea LTD supplies premium ceremonial attire for universities, legal professionals, and church communities in Kenya. We combine quality tailoring, consistent sizing, and responsive support for smooth event execution."
        />
        <p class="mt-4 max-w-3xl text-zinc-600">
            Our collection includes preschool, certificate, diploma, degree, masters, and PhD graduation attire. If you do not find what you need, we provide custom stitching tailored to your institution or event.
        </p>
    </section>

    <section class="container-shell section-md">
        <div class="luxury-grid md:grid-cols-3">
            <article class="surface p-6">
                <h3 class="font-semibold">Wide Selection</h3>
                <p class="mt-3 text-sm text-zinc-600">Thousands of gowns and accessories in academic and ceremonial variants.</p>
            </article>
            <article class="surface p-6">
                <h3 class="font-semibold">Affordable &amp; Reliable</h3>
                <p class="mt-3 text-sm text-zinc-600">Flexible packages and dependable delivery for institutions and individuals.</p>
            </article>
            <article class="surface p-6">
                <h3 class="font-semibold">Professional Support</h3>
                <p class="mt-3 text-sm text-zinc-600">Hands-on support from inquiry to delivery and return.</p>
            </article>
        </div>
    </section>

    <x-ui.cta-band
        title="Plan your ceremony with confidence"
        description="Our team helps institutions and individuals choose the right attire mix for every event scale."
        primaryLabel="Contact Us"
        :primaryHref="route('contact-us')"
        secondaryLabel="Bulk Hire"
        :secondaryHref="route('bulk-inquiry')"
    />
@endsection
