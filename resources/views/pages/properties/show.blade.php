@php
    $category = $property['category'] ?? 'graduation';
    $backHref = match ($category) {
        'legal' => route('legal-attire'),
        'church' => route('church-wear'),
        default => route('graduation-attire'),
    };
    $backLabel = match ($category) {
        'legal' => 'Legal Attire',
        'church' => 'Church Wear',
        default => 'Graduation Attire',
    };
    $gallery = $property['gallery'] ?? [$property['image'] ?? '/images/site/hero.webp'];
    $options = $property['options'] ?? [];
    $details = $property['details'] ?? [];
    $sizeGuide = $property['size_guide'] ?? [];
    $optionDefaults = collect($options)->map(fn ($values) => $values[0] ?? '')->all();
    $productUrl = url()->current();
@endphp

@extends('layouts.public')

@section('content')
    <section
        class="container-shell section-lg"
        x-data="{
            gallery: {{ \Illuminate\Support\Js::from($gallery) }},
            active: 0,
            qty: 1,
            intent: '',
            submitting: false,
            sent: false,
            status: '',
            errors: {},
            form: {
                name: '',
                email: '',
                phone: '',
                message: '',
                website: '',
                company: '',
                form_token: {{ \Illuminate\Support\Js::from(\App\Support\InquiryFormGuard::token()) }},
            },
            options: {{ \Illuminate\Support\Js::from($optionDefaults) }},
            productTitle: {{ \Illuminate\Support\Js::from($property['title']) }},
            productPrice: {{ \Illuminate\Support\Js::from($property['price']) }},
            productUrl: {{ \Illuminate\Support\Js::from($productUrl) }},
            submitUrl: {{ \Illuminate\Support\Js::from(route('assistant.submit')) }},
            composedMessage() {
                const selected = Object.entries(this.options).map(([key, value]) => key + ': ' + value).join(', ');
                const action = this.intent === 'hire' ? 'hire' : 'purchase';
                return 'I would like to ' + action + ' ' + this.productTitle + ' (' + this.productPrice + ').\\n' + selected + '\\nQuantity: ' + this.qty + '\\nProduct: ' + this.productUrl + '\\nCeremony date:';
            },
            whatsappHref() {
                const action = this.intent === 'hire' ? 'hire' : 'purchase';
                const selected = Object.entries(this.options).map(([key, value]) => key + ': ' + value).join(', ');
                const text = 'Hello Gownsea, I want to ' + action + ' ' + this.productTitle + ' (' + this.productPrice + '). ' + selected + '. Qty: ' + this.qty + '. ' + this.productUrl;
                return 'https://wa.me/{{ config('gownsea.brand.whatsapp') }}?text=' + encodeURIComponent(text);
            },
            open(intent) {
                this.intent = intent;
                this.status = '';
                this.sent = false;
                this.errors = {};
                this.form.message = this.composedMessage();
                document.body.classList.add('overflow-hidden');
            },
            close() {
                this.intent = '';
                this.submitting = false;
                document.body.classList.remove('overflow-hidden');
            },
            async submitInquiry() {
                this.submitting = true;
                this.status = '';
                this.errors = {};
                try {
                    const { data } = await window.axios.post(this.submitUrl, this.form);
                    this.status = data.message;
                    this.sent = true;
                    this.form.name = '';
                    this.form.email = '';
                    this.form.phone = '';
                    this.form.message = this.composedMessage();
                } catch (error) {
                    this.errors = error.response?.data?.errors || {};
                    if (!Object.keys(this.errors).length) {
                        this.status = error.response?.data?.message || 'Something went wrong. Please try again.';
                    }
                } finally {
                    this.submitting = false;
                }
            }
        }"
        @keydown.escape.window="if (intent) close()"
    >
        <p class="mb-6 text-sm text-zinc-500">
            Home
            <span class="px-2">/</span>
            <a href="{{ $backHref }}" class="transition-colors hover:text-[#d42127]">{{ $backLabel }}</a>
            <span class="px-2">/</span>
            <span class="text-zinc-800">{{ $property['title'] }}</span>
        </p>

        <div class="grid items-start gap-10 lg:grid-cols-2">
            <div>
                <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-50">
                    <img
                        :src="gallery[active]"
                        alt="{{ $property['title'] }}"
                        class="aspect-square w-full object-contain p-6 md:p-10"
                    >
                </div>
                @if (count($gallery) > 1)
                    <div class="mt-4 flex gap-3 overflow-x-auto">
                        @foreach ($gallery as $index => $image)
                            <button
                                type="button"
                                class="h-20 w-20 shrink-0 overflow-hidden rounded-xl border bg-zinc-50"
                                :class="active === {{ $index }} ? 'border-[#d42127]' : 'border-zinc-200'"
                                @click="active = {{ $index }}"
                            >
                                <img src="{{ $image }}" alt="" class="h-full w-full object-contain p-1">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h1 class="font-semibold">{{ $property['title'] }}</h1>
                <p class="mt-4 text-3xl font-semibold text-zinc-950">{{ $property['price'] }}</p>
                <p class="mt-2 text-sm text-zinc-500">Tax included. Delivery calculated with your quote.</p>

                <div class="mt-8 space-y-6">
                    @foreach ($options as $label => $values)
                        <div>
                            <p class="text-sm font-semibold text-zinc-900">
                                {{ $label }}:
                                <span class="font-normal" x-text="options[{{ \Illuminate\Support\Js::from($label) }}]"></span>
                            </p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($values as $value)
                                    <button
                                        type="button"
                                        class="product-option-btn"
                                        :class="options[{{ \Illuminate\Support\Js::from($label) }}] === {{ \Illuminate\Support\Js::from($value) }} ? 'is-active' : ''"
                                        @click="options[{{ \Illuminate\Support\Js::from($label) }}] = {{ \Illuminate\Support\Js::from($value) }}"
                                    >
                                        {{ $value }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <label class="block text-sm font-semibold text-zinc-900">
                        Quantity
                        <input
                            type="number"
                            min="1"
                            x-model.number="qty"
                            class="mt-2 w-28 rounded-lg border border-zinc-300 px-3 py-3 text-sm font-normal"
                        >
                    </label>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-3">
                    <button type="button" class="btn-primary w-full gap-2 px-3" @click="open('purchase')">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 7h12l-1 12H7L6 7Z" stroke="currentColor" stroke-width="1.7"/><path d="M9 7V6a3 3 0 0 1 6 0v1" stroke="currentColor" stroke-width="1.7"/></svg>
                        Request this item
                    </button>
                    <button type="button" class="btn-secondary w-full gap-2 px-3" @click="open('hire')">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 3v4M16 3v4M4 10h16" stroke="currentColor" stroke-width="1.7"/></svg>
                        Hire this item
                    </button>
                </div>
            </div>
        </div>

        <div
            x-show="intent !== ''"
            x-cloak
            class="product-inquiry-overlay"
            role="dialog"
            aria-modal="true"
        >
            <div class="product-inquiry-overlay__backdrop" @click="close()"></div>
            <div
                class="product-inquiry-modal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 md:translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                @click.stop
            >
                <div class="product-inquiry-modal__header">
                    <div class="flex min-w-0 items-start gap-3 pr-2">
                        <span class="product-inquiry-modal__icon" aria-hidden="true">
                            <svg x-show="intent !== 'hire'" class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M6 7h12l-1 12H7L6 7Z" stroke="currentColor" stroke-width="1.7"/><path d="M9 7V6a3 3 0 0 1 6 0v1" stroke="currentColor" stroke-width="1.7"/></svg>
                            <svg x-show="intent === 'hire'" class="h-5 w-5" viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 3v4M16 3v4M4 10h16" stroke="currentColor" stroke-width="1.7"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#d42127]" x-text="intent === 'hire' ? 'Hire enquiry' : 'Purchase enquiry'"></p>
                            <h2 class="mt-1 text-lg font-semibold leading-snug sm:text-2xl" x-text="intent === 'hire' ? 'Hire {{ $property['title'] }}' : 'Request {{ $property['title'] }}'"></h2>
                            <p class="mt-1 text-xs text-zinc-600 sm:text-sm">Includes the colour, size, and quantity selected on this page.</p>
                        </div>
                    </div>
                    <button type="button" class="product-inquiry-modal__close" aria-label="Close enquiry form" @click="close()">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                </div>

                <div x-show="sent" class="product-inquiry-success">
                    <span class="product-inquiry-success__icon" aria-hidden="true">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M5 12.5 9.5 17 19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <h3 class="mt-3 text-lg font-semibold">Enquiry sent</h3>
                    <p class="mt-2 text-sm text-zinc-600" x-text="status"></p>
                    <button type="button" class="btn-primary mt-6 w-full gap-2" @click="close()">
                        Close
                    </button>
                </div>

                <form x-show="!sent" class="grid gap-3 sm:gap-4" @submit.prevent="submitInquiry()">
                    <div class="hp-field" aria-hidden="true">
                        <label>Website <input type="text" name="website" x-model="form.website" tabindex="-1" autocomplete="off"></label>
                        <label>Company <input type="text" name="company" x-model="form.company" tabindex="-1" autocomplete="off"></label>
                    </div>
                    <input type="hidden" name="form_token" x-model="form.form_token">

                    <label class="product-inquiry-field">
                        Full Name
                        <span class="product-inquiry-field__control">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/><path d="M5.5 19c1.2-3 3.6-4.5 6.5-4.5s5.3 1.5 6.5 4.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                            <input required x-model="form.name" type="text" autocomplete="name" placeholder="Your full name">
                        </span>
                        <span class="product-inquiry-field__error" x-show="errors.name" x-text="errors.name?.[0]"></span>
                    </label>
                    <label class="product-inquiry-field">
                        Email
                        <span class="product-inquiry-field__control">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="6" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="m5 8 7 5 7-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <input required x-model="form.email" type="email" autocomplete="email" placeholder="you@email.com">
                        </span>
                        <span class="product-inquiry-field__error" x-show="errors.email" x-text="errors.email?.[0]"></span>
                    </label>
                    <label class="product-inquiry-field">
                        Phone
                        <span class="product-inquiry-field__control">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7.5 4.8h3.2l1 3.2-1.8 1.2a12.5 12.5 0 0 0 5.9 5.9l1.2-1.8 3.2 1v3.1c0 .7-.6 1.4-1.4 1.4C10.8 18.8 5.2 13.2 5.2 6.2c0-.8.7-1.4 1.4-1.4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                            <input required x-model="form.phone" type="tel" autocomplete="tel" placeholder="07xx xxx xxx">
                        </span>
                        <span class="product-inquiry-field__error" x-show="errors.phone" x-text="errors.phone?.[0]"></span>
                    </label>
                    <label class="product-inquiry-field">
                        Message
                        <textarea required x-model="form.message" rows="4"></textarea>
                        <span class="product-inquiry-field__error" x-show="errors.message" x-text="errors.message?.[0]"></span>
                    </label>
                    <p class="text-xs text-zinc-500" x-show="errors.form_token" x-text="errors.form_token?.[0]"></p>
                    <div class="product-inquiry-actions">
                        <button type="submit" class="btn-primary w-full gap-2" :disabled="submitting">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 12 20 5l-6.5 14-2.2-5.3L4 12Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                            <span x-text="submitting ? 'Sending…' : (intent === 'hire' ? 'Send hire request' : 'Send purchase request')"></span>
                        </button>
                        <a :href="whatsappHref()" target="_blank" rel="noopener noreferrer" class="btn-secondary w-full gap-2">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.02Zm-7.01 15.24h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.42 5.83c0 4.55-3.7 8.23-8.25 8.23Z"/></svg>
                            WhatsApp this item
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="product-story mt-16 border-t border-zinc-200 pt-12">
            <div class="grid items-start gap-12 lg:grid-cols-2 lg:gap-16">
                <div>
                    <h2>Product Details</h2>
                    <ul class="product-story__details mt-6">
                        @foreach ($details as $detail)
                            <li>{{ $detail }}</li>
                        @endforeach
                    </ul>

                    <h3 class="mt-10 text-xl font-semibold">About {{ $property['title'] }}</h3>
                    <p class="mt-4 max-w-2xl text-[17px] leading-8 text-zinc-600">{{ $property['about'] }}</p>
                </div>

                <div>
                    @if (count($sizeGuide))
                        <h3 class="text-xl font-semibold">Size guide</h3>
                        <div class="product-story__table-wrap mt-5">
                            <table class="product-story__table">
                                <thead>
                                    <tr>
                                        <th>Size</th>
                                        <th>Best for</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sizeGuide as $row)
                                        <tr>
                                            <td>{{ $row['size'] }}</td>
                                            <td>{{ $row['guide'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if (! empty($property['fit_note']))
                        <p class="mt-4 text-sm leading-6 text-zinc-500"><span class="font-semibold text-zinc-800">NB:</span> {{ $property['fit_note'] }}</p>
                    @endif

                    <h2 class="mt-10">Shipping &amp; Returns</h2>
                    <div class="product-story__table-wrap mt-5">
                        <table class="product-story__table">
                            <tbody>
                                <tr>
                                    <th>Nairobi</th>
                                    <td>Free delivery for confirmed hire and purchase orders.</td>
                                </tr>
                                <tr>
                                    <th>Nationwide</th>
                                    <td>Courier available. Timelines confirmed with your quote.</td>
                                </tr>
                                <tr>
                                    <th>Express</th>
                                    <td>Arranged for ceremony dates with advance notice.</td>
                                </tr>
                                <tr>
                                    <th>Hire returns</th>
                                    <td>Collection window agreed with our team. <a href="{{ route('return-policy') }}">Return policy</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="product-story__trust mt-14 grid gap-8 border-t border-zinc-200 pt-10 sm:grid-cols-3">
                <article class="min-w-0">
                    <h3 class="text-lg font-semibold leading-snug">Gownsea represents these moments</h3>
                    <p class="mt-3 text-sm leading-6 text-zinc-600">Ceremonial garments go beyond fabric. We supply attire that carries the meaning of the day.</p>
                </article>
                <article class="min-w-0">
                    <h3 class="text-lg font-semibold leading-snug">We use sustainable thinking</h3>
                    <p class="mt-3 text-sm leading-6 text-zinc-600">Durable hire stock and quality materials reduce waste while keeping every look ceremony-ready.</p>
                </article>
                <article class="min-w-0">
                    <h3 class="text-lg font-semibold leading-snug">Delivery across Kenya</h3>
                    <p class="mt-3 text-sm leading-6 text-zinc-600">Nairobi delivery is complimentary on confirmed orders, with courier options for other counties.</p>
                </article>
            </div>
        </div>
    </section>

    @if (! empty($related))
        <section class="container-shell section-md">
            <x-ui.section-header kicker="You may also like" title="Related attire" />
            <div class="luxury-grid mt-8 md:grid-cols-4">
                @foreach ($related as $item)
                    <x-ui.property-card :property="$item" />
                @endforeach
            </div>
        </section>
    @endif
@endsection
