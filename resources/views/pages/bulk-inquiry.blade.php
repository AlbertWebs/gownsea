@extends('layouts.public')

@section('content')
    <section class="container-shell section-lg">
        <div class="luxury-grid items-start md:grid-cols-[1fr_1.05fr] md:gap-10">
            <div>
                <x-ui.section-header
                    kicker="Institutional Services"
                    title="Bulk Hire for Universities, Colleges, and Event Organizers"
                    description="Get tailored pricing for graduation gowns and accessories with dependable delivery support. Share your quantities, timeline, and ceremony details and our team will follow up quickly."
                />

                <div class="luxury-grid mt-8 md:grid-cols-2">
                    <article class="surface p-5">
                        <h3 class="text-base font-semibold">Volume-based pricing</h3>
                        <p class="mt-2 text-sm text-zinc-600">Competitive rates for large ceremony orders with transparent package options.</p>
                    </article>
                    <article class="surface p-5">
                        <h3 class="text-base font-semibold">Reliable timelines</h3>
                        <p class="mt-2 text-sm text-zinc-600">Planning support for fitting, dispatch, returns, and ceremony-day readiness.</p>
                    </article>
                    <article class="surface p-5">
                        <h3 class="text-base font-semibold">Custom requirements</h3>
                        <p class="mt-2 text-sm text-zinc-600">Institution-specific requests for color, quantities, and ceremony structure.</p>
                    </article>
                    <article class="surface p-5">
                        <h3 class="text-base font-semibold">Dedicated support</h3>
                        <p class="mt-2 text-sm text-zinc-600">A responsive team to guide your procurement process from inquiry to delivery.</p>
                    </article>
                </div>
            </div>

            <div class="surface p-6 md:p-8">
                <h3 class="font-semibold">Request bulk pricing</h3>
                <p class="mt-2 text-sm text-zinc-600">Tell us what you need and we will send a tailored quote.</p>

                <form method="POST" action="{{ route('assistant.submit') }}" class="mt-5 grid gap-4 md:grid-cols-2">
                    @csrf
                    <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">

                    <label class="text-xs font-semibold text-zinc-700">
                        Full Name
                        <input required name="name" class="mt-2 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm" type="text" placeholder="Your name">
                    </label>

                    <label class="text-xs font-semibold text-zinc-700">
                        Email Address
                        <input required name="email" class="mt-2 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm" type="email" placeholder="you@institution.com">
                    </label>

                    <label class="text-xs font-semibold text-zinc-700 md:col-span-2">
                        Mobile Number
                        <input required name="phone" class="mt-2 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm" type="text" placeholder="Phone number">
                    </label>

                    <label class="text-xs font-semibold text-zinc-700 md:col-span-2">
                        Bulk Requirements
                        <textarea required name="message" class="mt-2 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm" rows="5" placeholder="Institution name, gown categories, quantity, event date, and delivery location"></textarea>
                    </label>

                    <button type="submit" class="btn-primary md:col-span-2">Send bulk inquiry</button>
                </form>
            </div>
        </div>
    </section>

    <section class="container-shell section-md">
        <x-ui.section-header
            kicker="How It Works"
            title="Simple 3-step institutional onboarding"
            description="We keep bulk gown planning clear and efficient for your team."
        />

        <div class="luxury-grid mt-8 md:grid-cols-3">
            <article class="surface p-6">
                <p class="kicker">Step 1</p>
                <h3 class="mt-2 text-lg font-semibold">Share your brief</h3>
                <p class="mt-2 text-sm text-zinc-600">Tell us your quantities, categories, location, and event timeline.</p>
            </article>
            <article class="surface p-6">
                <p class="kicker">Step 2</p>
                <h3 class="mt-2 text-lg font-semibold">Receive your quote</h3>
                <p class="mt-2 text-sm text-zinc-600">We prepare a tailored package with clear rates and delivery plan.</p>
            </article>
            <article class="surface p-6">
                <p class="kicker">Step 3</p>
                <h3 class="mt-2 text-lg font-semibold">Confirm and execute</h3>
                <p class="mt-2 text-sm text-zinc-600">Our team coordinates fulfillment and supports your ceremony logistics.</p>
            </article>
        </div>
    </section>

    <x-ui.cta-band
        title="Need immediate assistance?"
        description="Call or message our support team for urgent ceremony timelines and fast bulk planning."
        primaryLabel="Contact Us"
        :primaryHref="route('contact-us')"
        secondaryLabel="Chat on WhatsApp"
        :secondaryHref="'https://wa.me/' . config('gownsea.brand.whatsapp')"
    />
@endsection
