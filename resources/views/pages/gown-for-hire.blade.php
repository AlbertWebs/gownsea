@extends('layouts.public')

@section('content')
    <x-ui.page-banner
        title="Graduation Gown For Hire"
        subtitle="Quality gown hire for your graduation"
        ctaLabel="Request Gown Now"
        ctaHref="#hire"
        image="/images/site/pexels-olia-danilevich-8093039.jpg"
        alt="Graduation gowns available for hire in Kenya"
    />

    <section id="hire" class="container-shell section-lg scroll-mt-24">
        <x-ui.section-header
            kicker="Gown Hire"
            title="Hire graduation attire in Kenya"
            description="Affordable gown rental for preschool through PhD, with accessories and institutional bulk options."
        />

        <div class="luxury-grid mt-8 md:grid-cols-3">
            @foreach ($properties as $property)
                <x-ui.property-card :property="$property" />
            @endforeach
        </div>

        <article class="surface-muted mt-12 border-l-4 border-l-[#0f2744] p-6 md:p-8">
            <p class="kicker">Institutions</p>
            <h3 class="mt-3 font-semibold text-[#0f2744]">Are you an institution?</h3>
            <p class="mt-3 max-w-3xl text-sm text-zinc-600">
                Gownsea LTD invites institutions to bulk hire or purchase high-quality graduation gowns at discounted rates.
                Ensure your graduates look their best with premium, comfortable, and customizable gowns.
            </p>
            <a href="{{ route('bulk-inquiry') }}" class="btn-primary mt-6">Start Bulk Inquiry</a>
        </article>

        @include('partials.clients')

        @if (! empty($faqs))
            <div class="mt-12">
                <x-ui.section-header kicker="FAQs" title="Gown hire questions" />
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
        title="Ready to hire a gown?"
        description="Share your award level, quantity, and ceremony date and we will confirm availability quickly."
        primaryLabel="Contact Us"
        :primaryHref="route('contact-us')"
        secondaryLabel="Shop Graduation Attire"
        :secondaryHref="route('graduation-attire')"
    />
@endsection
