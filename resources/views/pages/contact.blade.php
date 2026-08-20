@extends('layouts.public')

@php
    $brand = config('gownsea.brand');
    $phone = $brand['phone'] ?? '';
    $email = $brand['email'] ?? '';
    $address = $brand['address'] ?? '';
    $whatsapp = preg_replace('/\D+/', '', (string) ($brand['whatsapp'] ?? ''));
    $phoneHref = 'tel:'.preg_replace('/\s+/', '', (string) $phone);
    $mapsHref = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode((string) $address);
    $mapEmbed = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8201267846703!2d36.820048899999996!3d-1.2816737999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f116d69ae0cf1%3A0xd5afcb025a37d2dd!2sGOWNSEA%20%E2%80%93%20Graduation%20Gowns%20East%20Africa!5e0!3m2!1sen!2ske!4v1787133009013!5m2!1sen!2ske';
    $contactSchema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'ContactPage',
                '@id' => url()->current().'#webpage',
                'url' => url()->current(),
                'name' => $meta['title'],
                'description' => $meta['description'],
            ],
            [
                '@type' => 'LocalBusiness',
                'name' => 'Gownsea LTD',
                'image' => url('/images/site/hero.webp'),
                'url' => url('/'),
                'telephone' => $phone,
                'email' => $email,
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $address,
                    'addressLocality' => 'Nairobi',
                    'addressCountry' => 'KE',
                ],
                'openingHours' => 'Mo-Sa 08:00-18:00',
                'geo' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => -1.2816738,
                    'longitude' => 36.8200489,
                ],
            ],
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Contact Us', 'item' => url()->current()],
                ],
            ],
            [
                '@type' => 'FAQPage',
                'mainEntity' => collect($faqs)->map(fn ($answer, $question) => [
                    '@type' => 'Question',
                    'name' => $question,
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer],
                ])->values()->all(),
            ],
        ],
    ];
@endphp

@push('json_ld')
    <script type="application/ld+json">{!! json_encode($contactSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <div class="contact-page">
        <x-ui.page-banner
            title="Contact Us"
            subtitle="Visit the Nairobi showroom, call, or send a message for hire, purchase, and bulk ceremony support."
            ctaLabel="Send a message"
            ctaHref="#contact-form"
            image="/images/site/hero.webp"
            alt="Gownsea ceremonial attire showroom in Nairobi"
        />

        <nav class="container-shell pt-8 text-sm text-zinc-500" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('home') }}" class="transition-colors hover:text-[#d42127]">Home</a></li>
                <li aria-hidden="true">/</li>
                <li class="font-medium text-zinc-800">Contact Us</li>
            </ol>
        </nav>

        <section class="container-shell section-lg pt-8">
            <x-ui.section-header
                kicker="Get in touch"
                title="Talk to the Gownsea team"
                description="We help with graduation gown hire and sale, legal attire, church wear, custom stitching, and institutional bulk orders."
            />

            <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <a href="{{ $mapsHref }}" target="_blank" rel="noopener noreferrer" class="surface group block p-6">
                    <p class="kicker">Showroom</p>
                    <h3 class="mt-3 text-lg font-semibold transition-colors group-hover:text-[#d42127]">{{ $address }}</h3>
                    <p class="mt-2 text-sm text-zinc-600">Valji Building, Nairobi CBD. Open in Google Maps for directions.</p>
                </a>
                <a href="{{ $phoneHref }}" class="surface group block p-6">
                    <p class="kicker">Call</p>
                    <h3 class="mt-3 text-lg font-semibold transition-colors group-hover:text-[#d42127]">{{ $phone }}</h3>
                    <p class="mt-2 text-sm text-zinc-600">Monday to Saturday, 8am–6pm for fittings, quotes, and collection planning.</p>
                </a>
                <a href="mailto:{{ $email }}" class="surface group block p-6">
                    <p class="kicker">Email</p>
                    <h3 class="mt-3 text-lg font-semibold transition-colors group-hover:text-[#d42127]">{{ $email }}</h3>
                    <p class="mt-2 text-sm text-zinc-600">Send quantities, award levels, and your ceremony date for a written quote.</p>
                </a>
                @if ($whatsapp)
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" class="surface group block p-6">
                        <p class="kicker">WhatsApp</p>
                        <h3 class="mt-3 text-lg font-semibold transition-colors group-hover:text-[#d42127]">Chat with us</h3>
                        <p class="mt-2 text-sm text-zinc-600">Fastest way to confirm sizing, hire availability, and bulk support.</p>
                    </a>
                @endif
            </div>
        </section>

        <section class="bg-zinc-50 section-lg">
            <div class="container-shell">
                <div class="grid items-start gap-8 lg:grid-cols-[0.95fr_1.05fr]">
                    <div>
                        <x-ui.section-header
                            kicker="Nairobi CBD"
                            title="Find Gownsea on Moktar Daddah Street"
                            description="Come in for fittings and collections. The pin below is our Graduation Gowns East Africa showroom."
                        />
                        <ul class="mt-8 space-y-3 text-sm text-zinc-600">
                            <li>Showroom visits welcome during opening hours.</li>
                            <li>Bring your ceremony date and award level for faster fitting.</li>
                            <li>Bulk collections can be arranged in advance.</li>
                        </ul>
                        <a href="{{ $mapsHref }}" target="_blank" rel="noopener noreferrer" class="btn-secondary mt-6">Open in Google Maps</a>
                    </div>

                    <div class="surface overflow-hidden">
                        <iframe
                            src="{{ $mapEmbed }}"
                            title="Gownsea showroom map — Graduation Gowns East Africa, Nairobi"
                            width="600"
                            height="450"
                            class="h-[min(70vw,28rem)] w-full border-0 md:h-[450px]"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="strict-origin-when-cross-origin"
                        ></iframe>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact-form" class="container-shell section-lg scroll-mt-24">
            <div class="grid items-start gap-8 lg:grid-cols-[0.9fr_1.1fr]">
                <div>
                    <x-ui.section-header
                        kicker="Send a message"
                        title="We will follow up quickly"
                        description="Share what you need — hire, purchase, custom stitching, or bulk ceremony planning — and the Nairobi team will reply."
                    />
                    <div class="mt-8 space-y-3">
                        @foreach ($faqs as $question => $answer)
                            <details class="surface group p-5">
                                <summary class="cursor-pointer list-none font-semibold text-zinc-900">
                                    <span class="flex items-center justify-between gap-4">
                                        {{ $question }}
                                        <span class="text-zinc-400 transition group-open:rotate-45" aria-hidden="true">+</span>
                                    </span>
                                </summary>
                                <p class="mt-3 text-sm leading-relaxed text-zinc-600">{{ $answer }}</p>
                            </details>
                        @endforeach
                    </div>
                </div>

                <div class="surface border-t-4 border-t-[#0f2744] p-6 md:p-8">
                    <h3 class="text-xl font-semibold text-[#0f2744]">Contact form</h3>
                    <p class="mt-2 text-sm text-zinc-600">Tell us your name, how to reach you, and the details of your ceremony.</p>

                    <form method="POST" action="{{ route('assistant.submit') }}" class="mt-6 grid gap-4 sm:grid-cols-2">
                        @csrf
                        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
                        <input type="text" name="company" class="hidden" tabindex="-1" autocomplete="off">
                        <input type="hidden" name="form_token" value="{{ \App\Support\InquiryFormGuard::token() }}">

                        <label class="text-xs font-semibold text-zinc-700">
                            Full name
                            <input required name="name" class="mt-2 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#d42127]/50 focus:bg-white focus:ring-2 focus:ring-[#d42127]/15" type="text" autocomplete="name" placeholder="Your name">
                        </label>
                        <label class="text-xs font-semibold text-zinc-700">
                            Email
                            <input required name="email" class="mt-2 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#d42127]/50 focus:bg-white focus:ring-2 focus:ring-[#d42127]/15" type="email" autocomplete="email" placeholder="you@email.com">
                        </label>
                        <label class="text-xs font-semibold text-zinc-700 sm:col-span-2">
                            Phone
                            <input required name="phone" class="mt-2 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#d42127]/50 focus:bg-white focus:ring-2 focus:ring-[#d42127]/15" type="tel" autocomplete="tel" placeholder="+254 …">
                        </label>
                        <label class="text-xs font-semibold text-zinc-700 sm:col-span-2">
                            How can we help?
                            <textarea required name="message" class="mt-2 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#d42127]/50 focus:bg-white focus:ring-2 focus:ring-[#d42127]/15" rows="5" placeholder="Hire or purchase, award level, quantity, and ceremony date"></textarea>
                        </label>
                        <button type="submit" class="btn-primary sm:col-span-2">Send message</button>
                    </form>
                </div>
            </div>
        </section>

        <x-ui.cta-band
            title="Planning a large ceremony?"
            description="Share quantities and timelines for a bulk hire quote tailored to your institution."
            primaryLabel="Start bulk inquiry"
            :primaryHref="route('bulk-inquiry')"
            secondaryLabel="Shop graduation attire"
            :secondaryHref="route('graduation-attire')"
        />
    </div>
@endsection
