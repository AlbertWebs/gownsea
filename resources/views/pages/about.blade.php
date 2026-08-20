@extends('layouts.public')

@php
    $brand = config('gownsea.brand');
    $phone = $brand['phone'] ?? '';
    $email = $brand['email'] ?? '';
    $address = $brand['address'] ?? '';
    $whatsapp = preg_replace('/\D+/', '', (string) ($brand['whatsapp'] ?? ''));
    $phoneHref = 'tel:'.preg_replace('/\s+/', '', (string) $phone);
    $mapsHref = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode((string) $address);
    $aboutImage = url('/images/site/hero.webp');
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($faqs)->map(fn ($answer, $question) => [
            '@type' => 'Question',
            'name' => $question,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $answer,
            ],
        ])->values()->all(),
    ];
    $pageSchema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'AboutPage',
                '@id' => url()->current().'#webpage',
                'url' => url()->current(),
                'name' => $meta['title'],
                'description' => $meta['description'],
                'isPartOf' => ['@id' => url('/').'#organization'],
                'about' => ['@id' => url('/').'#organization'],
                'primaryImageOfPage' => $aboutImage,
                'inLanguage' => 'en-KE',
            ],
            [
                '@type' => 'Organization',
                '@id' => url('/').'#organization',
                'name' => 'Gownsea LTD',
                'url' => url('/'),
                'telephone' => $phone,
                'email' => $email,
                'image' => $aboutImage,
                'logo' => url('/favicon.ico'),
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $address,
                    'addressLocality' => 'Nairobi',
                    'addressCountry' => 'KE',
                ],
                'areaServed' => [
                    '@type' => 'Country',
                    'name' => 'Kenya',
                ],
                'sameAs' => [
                    'https://facebook.com',
                    'https://instagram.com',
                    'https://tiktok.com',
                    'https://linkedin.com',
                ],
            ],
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => url('/'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => 'About Us',
                        'item' => url()->current(),
                    ],
                ],
            ],
        ],
    ];
@endphp

@push('json_ld')
    <script type="application/ld+json">{!! json_encode($pageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <div class="about-page">
    <x-ui.page-banner
        title="About Gownsea"
        subtitle="Kenya’s ceremonial attire specialists for graduation, legal, and church wear."
        ctaLabel="Visit our Nairobi showroom"
        ctaHref="#visit"
        image="/images/site/hero.webp"
        alt="Gownsea graduation gowns and ceremonial attire in Nairobi, Kenya"
    />

    <nav class="container-shell pt-8 text-sm text-zinc-500" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ route('home') }}" class="transition-colors hover:text-[#d42127]">Home</a></li>
            <li aria-hidden="true">/</li>
            <li class="font-medium text-zinc-800">About Us</li>
        </ol>
    </nav>

    <section class="container-shell section-lg pt-8">
        <div class="grid items-center gap-10 lg:grid-cols-2">
            <div>
                <p class="kicker">Who we are</p>
                <h2 class="mt-3 font-semibold">Premium ceremonial attire, prepared for Kenya’s most important days</h2>
                <p class="mt-5 text-zinc-600">
                    Gownsea LTD supplies high-quality graduation gowns, legal attire, and church garments for hire and sale.
                    We help universities, colleges, TVETs, advocates, churches, and individual graduates look ceremony-ready
                    without the stress of last-minute sourcing.
                </p>
                <p class="mt-4 text-zinc-600">
                    Our collection covers preschool, certificate, diploma, degree, master’s, and PhD graduation attire, plus
                    courtroom-ready legal wear and church garments. If a standard set does not match your institution, we
                    provide custom stitching tailored to your colours, award level, and event.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('graduation-attire') }}" class="btn-primary">Shop graduation attire</a>
                    <a href="{{ route('gown-for-hire') }}" class="btn-secondary">Hire a gown</a>
                </div>
            </div>

            <figure class="about-media surface overflow-hidden">
                <img
                    src="/images/site/hero.webp"
                    alt="University-standard graduation gowns from Gownsea in Nairobi"
                    width="1200"
                    height="800"
                    class="h-full w-full object-cover"
                    loading="lazy"
                    decoding="async"
                >
            </figure>
        </div>
    </section>

    <section class="bg-zinc-50 section-md">
        <div class="container-shell">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <article class="surface p-6">
                    <p class="text-3xl font-semibold text-[#d42127]">5,000+</p>
                    <h3 class="mt-2 text-lg font-semibold">Clean gowns in stock</h3>
                    <p class="mt-2 text-sm text-zinc-600">Academic colours and sizes for universities, colleges, TVETs, churches, and individuals.</p>
                </article>
                <article class="surface p-6">
                    <p class="text-3xl font-semibold text-[#d42127]">Hire &amp; sale</p>
                    <h3 class="mt-2 text-lg font-semibold">Flexible packages</h3>
                    <p class="mt-2 text-sm text-zinc-600">Rent a set for one ceremony or purchase attire for long-term institutional use.</p>
                </article>
                <article class="surface p-6">
                    <p class="text-3xl font-semibold text-[#d42127]">Custom</p>
                    <h3 class="mt-2 text-lg font-semibold">Made to specification</h3>
                    <p class="mt-2 text-sm text-zinc-600">Custom stitching when your faculty colours or award level need a unique finish.</p>
                </article>
                <article class="surface p-6">
                    <p class="text-3xl font-semibold text-[#d42127]">Nairobi</p>
                    <h3 class="mt-2 text-lg font-semibold">Showroom support</h3>
                    <p class="mt-2 text-sm text-zinc-600">Visit Valji Building, Moktar Daddah Street, Monday to Saturday, 8am–6pm.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="container-shell section-lg">
        <x-ui.section-header
            kicker="What we supply"
            title="Attire for every ceremony"
            description="One Nairobi team for graduation, legal, and church wear — with hire, purchase, and bulk planning in the same place."
        />

        <div class="luxury-grid mt-8 md:grid-cols-3">
            <a href="{{ route('graduation-attire') }}" class="surface group flex h-full flex-col p-6">
                <p class="kicker">Graduation attire</p>
                <h3 class="mt-3 text-xl font-semibold transition-colors group-hover:text-[#d42127]">Gowns, caps, hoods, and stoles</h3>
                <p class="mt-3 flex-1 text-sm text-zinc-600">University-standard sets from preschool to PhD, with academic colours, tassels, and complete hire packages.</p>
                <span class="mt-5 text-sm font-semibold underline">Browse graduation attire <span class="about-arrow inline-block">→</span></span>
            </a>
            <a href="{{ route('legal-attire') }}" class="surface group flex h-full flex-col p-6">
                <p class="kicker">Legal wear</p>
                <h3 class="mt-3 text-xl font-semibold transition-colors group-hover:text-[#d42127]">Courtroom-ready professional dress</h3>
                <p class="mt-3 flex-1 text-sm text-zinc-600">Advocates’ robes, shirts, bibs, and related legal attire prepared for a polished professional finish.</p>
                <span class="mt-5 text-sm font-semibold underline">Browse legal attire <span class="about-arrow inline-block">→</span></span>
            </a>
            <a href="{{ route('church-wear') }}" class="surface group flex h-full flex-col p-6">
                <p class="kicker">Church wear</p>
                <h3 class="mt-3 text-xl font-semibold transition-colors group-hover:text-[#d42127]">Clergy and choral garments</h3>
                <p class="mt-3 flex-1 text-sm text-zinc-600">Church and choir wear designed for reverence, unity, and comfortable ceremony use.</p>
                <span class="mt-5 text-sm font-semibold underline">Browse church wear <span class="about-arrow inline-block">→</span></span>
            </a>
        </div>
    </section>

    <section class="container-shell section-md">
        <x-ui.section-header
            kicker="Academic levels"
            title="Graduation attire for every award"
            description="We stock and stitch sets across the full academic path, so institutions can dress mixed cohorts from one supplier."
        />
        <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['label' => 'Preschool', 'href' => '/our-products/preschool-graduation'],
                ['label' => 'Certificate', 'href' => '/shop-attire-collection/graduation-attire/certificate-gowns'],
                ['label' => 'Diploma', 'href' => '/shop-attire-collection/graduation-attire/diploma-gowns'],
                ['label' => 'Degree', 'href' => '/our-products/degree-graduation-gowns'],
                ['label' => 'Masters', 'href' => '/our-products/masters-gown'],
                ['label' => 'PhD', 'href' => '/our-products/phd-graduation-gown'],
            ] as $level)
                <a href="{{ $level['href'] }}" class="surface group flex items-center justify-between px-5 py-4 text-sm font-semibold text-zinc-800">
                    {{ $level['label'] }} graduation gowns
                    <span class="about-arrow" aria-hidden="true">→</span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="bg-zinc-50 section-lg">
        <div class="container-shell">
            <x-ui.section-header
                kicker="How we work"
                title="From inquiry to ceremony day"
                description="Whether you need one gown or several hundred, the process stays clear: choose hire or purchase, confirm sizes, and we handle the rest."
            />
            <div class="luxury-grid mt-8 md:grid-cols-4">
                <article class="surface p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#d42127]">01</p>
                    <h3 class="mt-3 text-lg font-semibold">Tell us the event</h3>
                    <p class="mt-2 text-sm text-zinc-600">Share the date, award levels, quantities, and whether you need hire, purchase, or a mix.</p>
                </article>
                <article class="surface p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#d42127]">02</p>
                    <h3 class="mt-3 text-lg font-semibold">Match the set</h3>
                    <p class="mt-2 text-sm text-zinc-600">We confirm gowns, caps, hoods, stoles, and colours so the cohort looks consistent on the day.</p>
                </article>
                <article class="surface p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#d42127]">03</p>
                    <h3 class="mt-3 text-lg font-semibold">Fit and deliver</h3>
                    <p class="mt-2 text-sm text-zinc-600">Visit the Nairobi showroom or arrange delivery. We help with sizing so returns stay simple.</p>
                </article>
                <article class="surface p-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#d42127]">04</p>
                    <h3 class="mt-3 text-lg font-semibold">Ceremony support</h3>
                    <p class="mt-2 text-sm text-zinc-600">Hire sets come back after the event. Purchase orders stay with you for future conferments.</p>
                </article>
            </div>
        </div>
    </section>

    <section id="visit" class="container-shell section-lg scroll-mt-24">
        <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
            <div>
                <x-ui.section-header
                    kicker="Visit us"
                    title="Gownsea showroom in Nairobi CBD"
                    description="Come in for fittings, bulk collections, and advice on academic colours. We are open Monday to Saturday, 8am–6pm."
                />
                <address class="mt-8 not-italic">
                    <div class="luxury-grid sm:grid-cols-2">
                        <div class="surface p-6">
                            <p class="text-sm font-semibold text-zinc-900">Address</p>
                            <p class="mt-2 text-sm text-zinc-600">{{ $address }}</p>
                            <a href="{{ $mapsHref }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-block text-sm font-semibold underline">Open in Google Maps</a>
                        </div>
                        <div class="surface p-6">
                            <p class="text-sm font-semibold text-zinc-900">Talk to the team</p>
                            <p class="mt-2 text-sm text-zinc-600"><a class="hover:text-[#d42127]" href="{{ $phoneHref }}">{{ $phone }}</a></p>
                            <p class="mt-1 text-sm text-zinc-600"><a class="hover:text-[#d42127]" href="mailto:{{ $email }}">{{ $email }}</a></p>
                            @if ($whatsapp)
                                <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-block text-sm font-semibold underline">Message on WhatsApp</a>
                            @endif
                        </div>
                    </div>
                </address>
            </div>
            <aside class="surface-muted border-l-4 border-l-[#0f2744] p-6 md:p-8">
                <h3 class="text-xl font-semibold text-[#0f2744]">Why institutions choose Gownsea</h3>
                <ul class="mt-5 space-y-3 text-sm text-zinc-600">
                    <li>Competitive hire rates with timely Nairobi-area delivery.</li>
                    <li>Consistent sizing support for mixed award-level cohorts.</li>
                    <li>Friendly, responsive help from inquiry through return.</li>
                    <li>Custom stitching when standard stock is not enough.</li>
                    <li>One supplier for graduation, legal, and church ceremonies.</li>
                </ul>
                <a href="{{ route('bulk-inquiry') }}" class="btn-primary mt-6">Start a bulk inquiry</a>
            </aside>
        </div>
    </section>

    @include('partials.clients')

    @if (count($posts))
        <section class="container-shell section-md">
            <x-ui.section-header
                kicker="The Gown Journal"
                title="Planning notes from the Gownsea team"
                description="Practical guidance on bulk hire, renting versus buying, and what graduates notice on ceremony day."
            />
            <div class="luxury-grid mt-8 md:grid-cols-2">
                @foreach ($posts as $post)
                    <a href="{{ route('journal.show', $post['slug']) }}" class="surface group block p-6">
                        <p class="kicker">{{ $post['category'] }}</p>
                        <h3 class="mt-2 font-semibold transition-colors group-hover:text-[#d42127]">{{ $post['title'] }}</h3>
                        <p class="mt-3 text-sm text-zinc-600">{{ $post['excerpt'] }}</p>
                        <span class="mt-4 inline-block text-sm font-semibold underline">Read article <span class="about-arrow inline-block">→</span></span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="container-shell section-lg">
        <x-ui.section-header
            kicker="FAQs"
            title="Common questions about Gownsea"
            description="Short answers before you call, visit, or send a bulk request."
        />
        <div class="mt-8 space-y-3">
            @foreach ($faqs as $question => $answer)
                <details class="surface group p-5 transition duration-300 hover:border-[#d42127]/40">
                    <summary class="cursor-pointer list-none font-semibold text-zinc-900 marker:content-none">
                        <span class="flex items-center justify-between gap-4">
                            {{ $question }}
                            <span class="text-zinc-400 transition-transform duration-300 group-hover:text-[#d42127] group-open:rotate-45" aria-hidden="true">+</span>
                        </span>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-zinc-600">{{ $answer }}</p>
                </details>
            @endforeach
        </div>
    </section>

    <x-ui.cta-band
        title="Plan your ceremony with Gownsea"
        description="Talk to the Nairobi team for hire, purchase, custom stitching, or institutional bulk planning."
        primaryLabel="Contact us"
        :primaryHref="route('contact-us')"
        secondaryLabel="Bulk hire"
        :secondaryHref="route('bulk-inquiry')"
    />
    </div>
@endsection
