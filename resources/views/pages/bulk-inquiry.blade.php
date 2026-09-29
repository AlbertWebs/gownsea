@extends('layouts.public')

@section('content')
    <div
        class="bulk-page"
        x-data="{
            mathOpen: false,
            mathAnswer: '',
            mathError: @js($errors->first('math_answer') ?: ''),
            sending: false,
            sent: false,
            status: '',
            submitErrors: {},
            submitted: {},
            submitUrl: @js(route('assistant.submit')),
            whatsappNumber: @js(preg_replace('/\D+/', '', (string) config('gownsea.brand.whatsapp'))),
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
            async confirmSend() {
                const value = String(this.mathAnswer).trim();
                if (value === '') {
                    this.mathError = 'Enter the answer to send your inquiry.';
                    return;
                }
                this.sending = true;
                this.mathError = '';
                this.submitErrors = {};
                const payload = Object.fromEntries(new FormData(this.$refs.form).entries());
                payload.math_answer = value;
                try {
                    const { data } = await window.axios.post(this.submitUrl, payload);
                    this.status = data.message || 'Your bulk enquiry has been sent. We will contact you shortly.';
                    this.submitted = {
                        name: payload.name,
                        email: payload.email,
                        phone: payload.phone,
                        message: payload.message,
                    };
                    this.sent = true;
                    this.mathOpen = false;
                } catch (error) {
                    this.submitErrors = error.response?.data?.errors || {};
                    if (this.submitErrors.math_answer || this.submitErrors.math_token) {
                        this.mathError = this.submitErrors.math_answer?.[0] || this.submitErrors.math_token?.[0] || 'Please try the spam check again.';
                    } else {
                        this.mathOpen = false;
                        this.status = error.response?.data?.message || 'We could not send your enquiry. Please check the form and try again.';
                    }
                } finally {
                    this.sending = false;
                }
            },
            whatsappHref() {
                const text = [
                    'Hello Gownsea, I have just submitted a bulk hire enquiry on your website.',
                    '',
                    'Name: ' + this.submitted.name,
                    'Email: ' + this.submitted.email,
                    'Phone: ' + this.submitted.phone,
                    '',
                    'Bulk requirements:',
                    this.submitted.message,
                ].join('\n');
                return 'https://wa.me/' + this.whatsappNumber + '?text=' + encodeURIComponent(text);
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

                    @if ($bulkSubmission = session('bulk_whatsapp_submission'))
                        @php
                            $bulkWhatsapp = preg_replace('/\D+/', '', (string) config('gownsea.brand.whatsapp'));
                            $bulkWhatsappMessage = implode("\n", [
                                'Hello Gownsea, I have just submitted a bulk hire enquiry on your website.',
                                '',
                                'Name: '.$bulkSubmission['name'],
                                'Email: '.$bulkSubmission['email'],
                                'Phone: '.$bulkSubmission['phone'],
                                '',
                                'Bulk requirements:',
                                $bulkSubmission['message'],
                            ]);
                        @endphp
                        <div role="status" class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-emerald-100 text-emerald-700" aria-hidden="true">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="m5 12.5 4.5 4.5L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <h4 class="mt-4 text-lg font-semibold text-[#0f2744]">Your bulk enquiry is on its way</h4>
                            <p class="mt-2 text-sm leading-6 text-zinc-600">We’ve received your details. Want to keep the conversation moving? Open WhatsApp and send the same enquiry directly to our team.</p>
                            <a href="https://wa.me/{{ $bulkWhatsapp }}?text={{ rawurlencode($bulkWhatsappMessage) }}" target="_blank" rel="noopener noreferrer" class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-[#128C7E] px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0f766e] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#128C7E]">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.02Zm-7.01 15.24h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.42 5.83c0 4.55-3.7 8.23-8.25 8.23Z"/></svg>
                                Continue on WhatsApp
                                <span aria-hidden="true">↗</span>
                            </a>
                            <p class="mt-2 text-center text-xs text-zinc-500">Your message is prefilled; tap send in WhatsApp to share it.</p>
                        </div>
                    @else
                    <div x-show="sent" x-cloak x-transition.opacity.duration.200ms class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-6" role="status" aria-live="polite">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-emerald-100 text-emerald-700" aria-hidden="true">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="m5 12.5 4.5 4.5L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <h4 class="mt-4 text-lg font-semibold text-[#0f2744]">Your bulk enquiry is on its way</h4>
                        <p class="mt-2 text-sm leading-6 text-zinc-600" x-text="status"></p>
                        <a :href="whatsappHref()" target="_blank" rel="noopener noreferrer" class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-[#128C7E] px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0f766e] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#128C7E]">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.02Zm-7.01 15.24h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.42 5.83c0 4.55-3.7 8.23-8.25 8.23Z"/></svg>
                            Continue on WhatsApp <span aria-hidden="true">↗</span>
                        </a>
                        <p class="mt-2 text-center text-xs text-zinc-500">Your submitted details are prefilled; tap send in WhatsApp to share them.</p>
                    </div>
                    <form
                        x-ref="form"
                        method="POST"
                        action="{{ route('assistant.submit') }}"
                        x-show="!sent"
                        x-transition.opacity.duration.150ms
                        :aria-busy="sending"
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

                        <p class="text-sm text-red-700 sm:col-span-2" x-show="status && !sent" x-text="status" aria-live="polite"></p>
                        <p class="text-sm text-red-700 sm:col-span-2" x-show="submitErrors.name" x-text="submitErrors.name?.[0]"></p>
                        <p class="text-sm text-red-700 sm:col-span-2" x-show="submitErrors.email" x-text="submitErrors.email?.[0]"></p>
                        <p class="text-sm text-red-700 sm:col-span-2" x-show="submitErrors.phone" x-text="submitErrors.phone?.[0]"></p>
                        <p class="text-sm text-red-700 sm:col-span-2" x-show="submitErrors.message" x-text="submitErrors.message?.[0]"></p>
                        <button type="submit" class="btn-primary sm:col-span-2" :disabled="sending">
                            <span x-show="!sending">Send bulk inquiry</span>
                            <span x-show="sending" class="inline-flex items-center gap-2"><svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity=".25" stroke-width="3"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>Sending your enquiry…</span>
                        </button>
                    </form>
                    @endif
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
                        :disabled="sending"
                    >
                </label>
                <p class="mt-2 text-sm text-[#d42127]" x-show="mathError" x-text="mathError"></p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="button" class="btn-primary" @click="confirmSend()" :disabled="sending">
                        <span x-text="sending ? 'Sending…' : 'Verify and send'"></span>
                    </button>
                    <button type="button" class="btn-secondary" @click="mathOpen = false" :disabled="sending">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endsection
