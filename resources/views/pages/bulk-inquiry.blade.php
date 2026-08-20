@extends('layouts.public')

@section('content')
    <div
        class="bulk-page"
        x-data="{
            mathOpen: false,
            mathAnswer: '',
            mathError: @js($errors->first('math_answer') ?: ''),
            details: {{ \Illuminate\Support\Js::from(old('details', '')) }},
            institution: {{ \Illuminate\Support\Js::from(old('institution', '')) }},
            quantity: {{ \Illuminate\Support\Js::from(old('quantity', '')) }},
            eventDate: {{ \Illuminate\Support\Js::from(old('event_date', '')) }},
            composeMessage() {
                const parts = [];
                if (this.institution) parts.push('Institution: ' + this.institution);
                if (this.quantity) parts.push('Estimated quantity: ' + this.quantity);
                if (this.eventDate) parts.push('Ceremony date: ' + this.eventDate);
                if (this.details) parts.push(this.details);
                return parts.join('\n');
            },
            requestSend() {
                if (! this.$refs.form.reportValidity()) return;
                this.mathAnswer = '';
                this.mathError = '';
                this.mathOpen = true;
                this.$nextTick(() => this.$refs.mathInput?.focus());
            },
            confirmSend() {
                const value = String(this.mathAnswer).trim();
                if (value === '') {
                    this.mathError = 'Enter the answer to send your inquiry.';
                    return;
                }
                this.mathOpen = false;
                this.$refs.form.submit();
            }
        }"
    >
        <x-ui.page-banner
            title="Bulk Hire"
            subtitle="Volume pricing and delivery planning for universities, colleges, TVETs, and churches."
            ctaLabel="Request a quote"
            ctaHref="#bulk-form"
            image="/images/site/hero.webp"
            alt="Bulk graduation gown hire for institutions in Kenya"
        />

        <nav class="container-shell pt-8 text-sm text-zinc-500" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('home') }}" class="transition-colors hover:text-[#d42127]">Home</a></li>
                <li aria-hidden="true">/</li>
                <li class="font-medium text-[#0f2744]">Bulk Hire</li>
            </ol>
        </nav>

        <section class="container-shell section-lg pt-8">
            <div class="grid items-start gap-10 lg:grid-cols-[0.95fr_1.05fr]">
                <div>
                    <x-ui.section-header
                        kicker="Institutional services"
                        title="Bulk hire for universities, colleges, and event organizers"
                        description="Share quantities, award levels, and your ceremony date. Gownsea will send a tailored hire quote with delivery and return planning."
                    />

                    <div class="luxury-grid mt-8 sm:grid-cols-2">
                        <article class="surface border-l-4 border-l-[#0f2744] p-5">
                            <h3 class="text-base font-semibold">Volume-based pricing</h3>
                            <p class="mt-2 text-sm text-zinc-600">Competitive rates for large ceremony orders with transparent package options.</p>
                        </article>
                        <article class="surface p-5">
                            <h3 class="text-base font-semibold text-[#0f2744]">Reliable timelines</h3>
                            <p class="mt-2 text-sm text-zinc-600">Planning support for fitting, dispatch, returns, and ceremony-day readiness.</p>
                        </article>
                        <article class="surface p-5">
                            <span class="mb-3 inline-block h-2 w-8 bg-[#0f2744]" aria-hidden="true"></span>
                            <h3 class="text-base font-semibold">Custom requirements</h3>
                            <p class="mt-2 text-sm text-zinc-600">Institution-specific colours, mixed award levels, and ceremony structure.</p>
                        </article>
                        <article class="surface bg-[#0f2744] p-5 text-white">
                            <h3 class="text-base font-semibold text-white">Dedicated support</h3>
                            <p class="mt-2 text-sm text-white/80">A responsive Nairobi team from inquiry through collection and return.</p>
                        </article>
                    </div>
                </div>

                <div id="bulk-form" class="surface scroll-mt-28 border-t-4 border-t-[#0f2744] p-6 md:p-8">
                    <h3 class="text-xl font-semibold text-[#0f2744]">Request bulk pricing</h3>
                    <p class="mt-2 text-sm text-zinc-600">Tell us what you need. A short maths check appears before the inquiry is sent.</p>

                    @if ($errors->any())
                        <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form
                        x-ref="form"
                        method="POST"
                        action="{{ route('assistant.submit') }}"
                        class="mt-5 grid gap-4 sm:grid-cols-2"
                        @submit.prevent="requestSend()"
                    >
                        @csrf
                        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
                        <input type="text" name="company" class="hidden" tabindex="-1" autocomplete="off">
                        <input type="hidden" name="form_token" value="{{ \App\Support\InquiryFormGuard::token() }}">
                        <input type="hidden" name="form_intent" value="bulk">
                        <input type="hidden" name="math_token" value="{{ $math['token'] }}">
                        <input type="hidden" name="math_answer" x-model="mathAnswer">
                        <input type="hidden" name="message" :value="composeMessage()">

                        <label class="text-xs font-semibold text-zinc-700">
                            Full name
                            <input required name="name" value="{{ old('name') }}" class="mt-2 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#d42127]/50 focus:bg-white focus:ring-2 focus:ring-[#d42127]/15" type="text" autocomplete="name" placeholder="Your name">
                        </label>
                        <label class="text-xs font-semibold text-zinc-700">
                            Email
                            <input required name="email" value="{{ old('email') }}" class="mt-2 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#d42127]/50 focus:bg-white focus:ring-2 focus:ring-[#d42127]/15" type="email" autocomplete="email" placeholder="you@institution.com">
                        </label>
                        <label class="text-xs font-semibold text-zinc-700">
                            Phone
                            <input required name="phone" value="{{ old('phone') }}" class="mt-2 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#d42127]/50 focus:bg-white focus:ring-2 focus:ring-[#d42127]/15" type="tel" autocomplete="tel" placeholder="+254 …">
                        </label>
                        <label class="text-xs font-semibold text-zinc-700">
                            Institution
                            <input x-model="institution" class="mt-2 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#d42127]/50 focus:bg-white focus:ring-2 focus:ring-[#d42127]/15" type="text" placeholder="University, college, or church">
                        </label>
                        <label class="text-xs font-semibold text-zinc-700">
                            Estimated quantity
                            <input x-model="quantity" class="mt-2 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#d42127]/50 focus:bg-white focus:ring-2 focus:ring-[#d42127]/15" type="text" placeholder="e.g. 200 gowns">
                        </label>
                        <label class="text-xs font-semibold text-zinc-700">
                            Ceremony date
                            <input x-model="eventDate" class="mt-2 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#d42127]/50 focus:bg-white focus:ring-2 focus:ring-[#d42127]/15" type="date">
                        </label>
                        <label class="text-xs font-semibold text-zinc-700 sm:col-span-2">
                            Bulk requirements
                            <textarea
                                required
                                minlength="10"
                                x-model="details"
                                class="mt-2 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#d42127]/50 focus:bg-white focus:ring-2 focus:ring-[#d42127]/15"
                                rows="5"
                                placeholder="Award levels, colours, delivery location, and any custom stitching notes"
                            ></textarea>
                        </label>

                        <button type="submit" class="btn-primary sm:col-span-2">Send bulk inquiry</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="bg-zinc-50 section-lg">
            <div class="container-shell">
                <x-ui.section-header
                    kicker="How it works"
                    title="Three steps from brief to ceremony day"
                    description="We keep bulk gown planning clear for procurement teams and event organisers."
                />
                <div class="luxury-grid mt-8 md:grid-cols-3">
                    <article class="surface p-6">
                        <p class="kicker">Step 1</p>
                        <h3 class="mt-2 text-lg font-semibold">Share your brief</h3>
                        <p class="mt-2 text-sm text-zinc-600">Tell us quantities, categories, location, and the event timeline.</p>
                    </article>
                    <article class="surface bg-[#0f2744] p-6 text-white">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Step 2</p>
                        <h3 class="mt-2 text-lg font-semibold text-white">Receive your quote</h3>
                        <p class="mt-2 text-sm text-white/80">We prepare a tailored package with rates and a delivery plan.</p>
                    </article>
                    <article class="surface p-6">
                        <p class="kicker">Step 3</p>
                        <h3 class="mt-2 text-lg font-semibold text-[#0f2744]">Confirm and execute</h3>
                        <p class="mt-2 text-sm text-zinc-600">Our team coordinates fulfillment and ceremony-day logistics.</p>
                    </article>
                </div>
            </div>
        </section>

        <x-ui.cta-band
            title="Need immediate assistance?"
            description="Call or message the Nairobi team for urgent ceremony timelines and fast bulk planning."
            primaryLabel="Contact us"
            :primaryHref="route('contact-us')"
            secondaryLabel="Chat on WhatsApp"
            :secondaryHref="'https://wa.me/' . config('gownsea.brand.whatsapp')"
        />

        <div
            x-show="mathOpen"
            x-cloak
            class="fixed inset-0 z-[80] flex items-center justify-center bg-zinc-950/50 p-4"
            @keydown.escape.window="mathOpen = false"
        >
            <div
                class="w-full max-w-md rounded-2xl border border-zinc-200 bg-white p-6 shadow-2xl"
                @click.outside="mathOpen = false"
                role="dialog"
                aria-modal="true"
                aria-labelledby="bulk-math-title"
            >
                <p class="kicker">Spam check</p>
                <h3 id="bulk-math-title" class="mt-2 text-xl font-semibold">Quick maths before we send</h3>
                <p class="mt-2 text-sm text-zinc-600">Solve this to confirm you are a person. What is <strong class="text-zinc-900">{{ $math['prompt'] }}</strong>?</p>

                <label class="mt-5 block text-xs font-semibold text-zinc-700">
                    Your answer
                    <input
                        x-ref="mathInput"
                        x-model="mathAnswer"
                        @keydown.enter.prevent="confirmSend()"
                        class="mt-2 w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-3 py-2.5 text-sm outline-none transition focus:border-[#d42127]/50 focus:bg-white focus:ring-2 focus:ring-[#d42127]/15"
                        type="text"
                        inputmode="numeric"
                        autocomplete="off"
                        placeholder="Enter the number"
                    >
                </label>
                <p class="mt-2 text-sm text-[#d42127]" x-show="mathError" x-text="mathError"></p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="button" class="btn-primary" @click="confirmSend()">Verify and send</button>
                    <button type="button" class="btn-secondary" @click="mathOpen = false">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection
