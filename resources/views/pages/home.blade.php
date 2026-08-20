@extends('layouts.public')

@push('json_ld')
    @php
        $orgSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Gownsea LTD',
            'url' => url('/'),
            'telephone' => config('gownsea.brand.phone'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => config('gownsea.brand.address'),
            ],
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($orgSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('content')
    <div class="home-page">
    <x-ui.hero-section
        kicker="Graduation gown hire in Nairobi, Kenya"
        title="Premium academic gowns for hire and sale, tailored for every ceremony."
        description="University-standard graduation attire, courtroom-ready legal wear, and church garments delivered with reliable support."
        primaryLabel="Purchase Now"
        :primaryHref="route('graduation-attire')"
        secondaryLabel="Hire Now"
        :secondaryHref="route('gown-for-hire')"
        :images="[
            [
                'src' => '/images/site/hero.webp',
                'label' => 'Graduation Attire',
                'headline' => 'University-standard graduation gowns tailored for every ceremony.',
            ],
            [
                'src' => '/images/site/Amazon-seller-lawyer-renaldo-matamoro-86JiKaHF4I8-unsplash-min.jpg',
                'label' => 'Legal Wear',
                'headline' => 'Courtroom-ready legal attire with a premium professional finish.',
            ],
            [
                'src' => '/images/site/clergy-wear.webp',
                'label' => 'Church Wear',
                'headline' => 'Elegant church and choral wear designed for reverence and unity.',
            ],
        ]"
    />

    <section class="container-shell section-md">
        <div class="luxury-grid md:grid-cols-3">
            <article class="surface p-6">
                <span class="mb-3 inline-block h-2 w-8 bg-[#d42127]" aria-hidden="true"></span>
                <p class="kicker">Graduation Attire</p>
                <h3 class="mt-3 font-semibold">University-standard graduation attire for hire and sale</h3>
                <a href="{{ route('graduation-attire') }}" class="mt-5 inline-block text-sm font-semibold underline">Shop Graduation Attire</a>
            </article>
            <article class="surface p-6">
                <p class="kicker">Legal Wear</p>
                <h3 class="mt-3 font-semibold">Courtroom-standard attire for advocates and legal professionals</h3>
                <a href="{{ route('legal-attire') }}" class="mt-5 inline-block text-sm font-semibold underline">Shop Legal Wear</a>
            </article>
            <article class="surface bg-[#0f2744] p-6 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Church Wear</p>
                <h3 class="mt-3 font-semibold text-white">Elegant church and choral wear for your community</h3>
                <a href="{{ route('church-wear') }}" class="mt-5 inline-block text-sm font-semibold text-white underline">Shop Church Wear</a>
            </article>
        </div>
    </section>

    <section class="bg-zinc-50 section-lg">
        <div class="container-shell">
            <x-ui.section-header
                kicker="Featured"
                title="Browse our top ceremonial collections"
                description="Explore Gownsea's most requested graduation gowns, legal attire, and church wear, curated for quality, comfort, and ceremony-ready confidence."
            />
            <div class="featured-collections luxury-grid mt-8 md:grid-cols-3">
                @foreach ($properties as $property)
                    <x-ui.property-card :property="$property" ratio="4:5" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="container-shell section-lg">
        <div class="story-bands">
            <x-ui.story-band
                kicker="Graduation Attire"
                title="University-standard graduation attire for hire and sale"
                description="Gownsea LTD is your trusted graduation gown specialist. We provide high-quality academic regalia for hire and sale, tailored to your institution, delivered on time, and ready for the ceremony. From bulk orders to custom designs, we help graduates look the part on their day."
                ctaLabel="Shop Graduation Attire"
                :ctaHref="route('graduation-attire')"
                image="/images/site/hero.webp"
                alt="University-standard graduation gowns from Gownsea in Nairobi"
            />

            <x-ui.story-band
                kicker="Legal Wear"
                title="Courtroom-standard attire for advocates"
                description="Premium barrister wigs, gowns, bibs, and advocates shirts for hire and sale. Expertly crafted legal attire built for comfort, presence, and a professional courtroom finish, with delivery support across Nairobi."
                ctaLabel="Shop Legal Wear"
                :ctaHref="route('legal-attire')"
                image="/images/site/Amazon-seller-lawyer-renaldo-matamoro-86JiKaHF4I8-unsplash-min.jpg"
                alt="Courtroom-ready legal attire for advocates and barristers"
                :reverse="true"
                tone="navy"
            />

            <x-ui.story-band
                kicker="Church Wear"
                title="Elegant church and choral wear for every congregation"
                description="Clergy robes, cassocks, choir gowns, and liturgical accessories for hire and sale. Personalise church and choir garments to suit your colours, sizes, and ceremony so every service looks unified and dignified."
                ctaLabel="Shop Church Wear"
                :ctaHref="route('church-wear')"
                image="/images/site/clergy-wear.webp"
                alt="Church and choral wear from Gownsea"
            />
        </div>
    </section>

    <section class="container-shell section-md">
        <div class="luxury-grid md:grid-cols-3">
            <div class="surface p-6">
                <span class="mb-3 inline-block h-2 w-8 bg-[#0f2744]" aria-hidden="true"></span>
                <h3 class="font-semibold">Wide Selection</h3>
                <p class="mt-3 text-sm text-zinc-600">Over 5,000 clean, high-quality gowns in various sizes and academic colors.</p>
            </div>
            <div class="surface p-6">
                <h3 class="font-semibold text-[#0f2744]">Affordable & Reliable</h3>
                <p class="mt-3 text-sm text-zinc-600">Competitive hire rates with timely delivery, flexible packages, and consistent service.</p>
            </div>
            <div class="surface bg-[#0f2744] p-6 text-white">
                <h3 class="font-semibold text-white">Professional Support</h3>
                <p class="mt-3 text-sm text-white/80">Friendly support team offering personalized assistance for stress-free ceremonies.</p>
            </div>
        </div>
    </section>

    @include('partials.clients')

    <section class="container-shell section-md">
        <x-ui.section-header
            kicker="The Gown Journal"
            title="Crafting confidence, one gown at a time"
            description="Read practical advice and trends on graduation attire and bulk ceremony planning."
        />

        <div class="luxury-grid mt-8 md:grid-cols-2">
            @foreach ($posts as $index => $post)
                @if ($index === 1)
                    <article class="surface bg-[#0f2744] p-6 text-white">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">{{ $post['category'] }}</p>
                        <h3 class="mt-2 font-semibold text-white">{{ $post['title'] }}</h3>
                        <p class="mt-3 text-sm text-white/80">{{ $post['excerpt'] }}</p>
                        <a href="{{ route('journal.show', $post['slug']) }}" class="mt-4 inline-block text-sm font-semibold text-white underline">Read article</a>
                    </article>
                @else
                    <article class="surface border-t-4 border-t-[#0f2744] p-6">
                        <p class="kicker">{{ $post['category'] }}</p>
                        <h3 class="mt-2 font-semibold text-[#0f2744]">{{ $post['title'] }}</h3>
                        <p class="mt-3 text-sm text-zinc-600">{{ $post['excerpt'] }}</p>
                        <a href="{{ route('journal.show', $post['slug']) }}" class="mt-4 inline-block text-sm font-semibold underline">Read article</a>
                    </article>
                @endif
            @endforeach
        </div>
    </section>

    <x-ui.cta-band
        title="Need graduation attire for your institution?"
        description="Get a tailored bulk quote with timeline planning, delivery support, and custom options."
        primaryLabel="Start Bulk Inquiry"
        :primaryHref="route('bulk-inquiry')"
        secondaryLabel="Hire a Gown"
        :secondaryHref="route('gown-for-hire')"
    />
    </div>
@endsection
