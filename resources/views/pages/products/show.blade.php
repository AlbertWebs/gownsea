@extends('layouts.public')

@section('content')
    <div
        x-data="{
            stkOpen: false,
            galleryOpen: false,
            galleryIndex: 0,
            gallery: @js($product['gallery']),
            loading: false,
            phone: '',
            message: '',
            error: '',
            openGallery(index) {
                this.galleryIndex = index;
                this.galleryOpen = true;
            },
            nextImage() {
                this.galleryIndex = (this.galleryIndex + 1) % this.gallery.length;
            },
            prevImage() {
                this.galleryIndex = (this.galleryIndex - 1 + this.gallery.length) % this.gallery.length;
            },
            async requestStk() {
                this.loading = true;
                this.message = '';
                this.error = '';
                try {
                    const response = await fetch('{{ route('payments.mpesa.stk') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            phone: this.phone,
                            amount: {{ (float) preg_replace('/[^\d.]/', '', $product['price']) ?: 1 }},
                            description: '{{ $product['title'] }}',
                            product_slug: '{{ $product['slug'] }}',
                        }),
                    });

                    const payload = await response.json();
                    if (!response.ok) {
                        this.error = payload.message || 'Failed to send STK push.';
                        return;
                    }

                    this.message = payload.message || 'STK push sent. Please check your phone.';
                } catch (e) {
                    this.error = 'Unable to send STK push right now. Please try again.';
                } finally {
                    this.loading = false;
                }
            },
        }"
    >
        <section class="container-shell section-lg">
            <div class="luxury-grid items-start md:grid-cols-[1.1fr_.9fr] md:gap-10">
                <div>
                    <p class="kicker">{{ $product['category'] }}</p>
                    <h1 class="mt-3 font-semibold text-[#0f2744]">{{ $product['title'] }}</h1>
                    <p class="mt-5 text-zinc-600">{{ $product['description'] }}</p>

                    <div class="mt-6 inline-flex items-center rounded-full bg-[#d42127]/10 px-4 py-2 text-sm font-semibold text-[#d42127]">
                        {{ $product['price'] }}
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <button type="button" class="btn-primary" @click="stkOpen = true">Buy With M-PESA</button>
                        <a href="https://wa.me/{{ config('gownsea.brand.whatsapp') }}" class="btn-secondary" target="_blank" rel="noopener noreferrer">Order on WhatsApp</a>
                        <a href="#gallery" class="btn-secondary">Explore Gallery</a>
                    </div>
                </div>

                <x-ui.responsive-image
                    :src="$product['gallery'][0]"
                    :alt="$product['title']"
                    ratio="3:2"
                    class="surface"
                    :priority="true"
                />
            </div>
        </section>

        <section id="gallery" class="container-shell section-md">
            <x-ui.section-header
                kicker="Product Gallery"
                :title="$product['title']"
                description="Browse detailed visuals of this attire category and choose the right fit for your ceremony."
            />

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($product['gallery'] as $index => $image)
                    <button type="button" class="text-left" @click="openGallery({{ $index }})">
                        <x-ui.responsive-image :src="$image" :alt="$product['title'] . ' gallery image'" ratio="4:3" class="surface cursor-zoom-in" />
                    </button>
                @endforeach
            </div>
        </section>

        <section class="container-shell section-md">
            <div class="luxury-grid md:grid-cols-3">
                @foreach ($product['benefits'] as $benefit)
                    <article class="surface p-6">
                        <h3 class="text-lg font-semibold">{{ $benefit['title'] }}</h3>
                        <p class="mt-3 text-sm text-zinc-600">{{ $benefit['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <x-ui.cta-band
            title="Need Graduation Attire?"
            description="Hire a gown today with fast response, quality assurance, and delivery support."
            primaryLabel="Hire a Gown"
            :primaryHref="route('bulk-inquiry')"
            secondaryLabel="Contact Support"
            :secondaryHref="route('contact-us')"
        />

        <div
            x-show="stkOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[80] flex items-center justify-center bg-zinc-900/55 px-4"
            @keydown.escape.window="stkOpen = false"
        >
            <div class="surface w-full max-w-md border-t-4 border-t-[#0f2744] p-6" @click.away="stkOpen = false">
                <h3 class="text-lg font-semibold text-[#0f2744]">Pay with M-PESA</h3>
                <p class="mt-2 text-sm text-zinc-600">Enter your Safaricom number to receive STK prompt.</p>
                <div class="mt-4">
                    <label class="text-xs font-semibold text-zinc-700">Phone Number (e.g. 07XXXXXXXX)</label>
                    <input
                        x-model="phone"
                        type="text"
                        class="mt-2 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm"
                        placeholder="07XXXXXXXX"
                    >
                </div>

                <template x-if="error">
                    <p class="mt-3 text-sm font-medium text-red-600" x-text="error"></p>
                </template>
                <template x-if="message">
                    <p class="mt-3 text-sm font-medium text-emerald-600" x-text="message"></p>
                </template>

                <div class="mt-5 flex items-center gap-3">
                    <button type="button" class="btn-primary" :disabled="loading || !phone" @click="requestStk()">
                        <span x-show="!loading">Send STK Prompt</span>
                        <span x-show="loading">Sending...</span>
                    </button>
                    <button type="button" class="btn-secondary" @click="stkOpen = false">Cancel</button>
                </div>
            </div>
        </div>

        <div
            x-show="galleryOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[90] bg-zinc-950/90 p-4 md:p-8"
            @keydown.escape.window="galleryOpen = false"
            @keydown.arrow-right.window="if (galleryOpen) nextImage()"
            @keydown.arrow-left.window="if (galleryOpen) prevImage()"
        >
            <div class="mx-auto flex h-full w-full max-w-6xl flex-col">
                <div class="mb-3 flex items-center justify-between text-white">
                    <p class="text-sm font-medium">{{ $product['title'] }} Gallery</p>
                    <button type="button" class="rounded-2xl border border-white/35 px-3 py-1 text-sm" @click="galleryOpen = false">Close</button>
                </div>

                <div class="relative flex-1 overflow-hidden rounded-2xl bg-black/30">
                    <template x-if="gallery.length">
                        <img
                            :src="gallery[galleryIndex]"
                            alt="Gallery image preview"
                            class="h-full w-full object-contain"
                        >
                    </template>

                    <button type="button" class="absolute left-3 top-1/2 -translate-y-1/2 rounded-2xl border border-white/30 bg-black/35 px-3 py-2 text-white" @click="prevImage()">‹</button>
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-2xl border border-white/30 bg-black/35 px-3 py-2 text-white" @click="nextImage()">›</button>
                </div>

                <div class="mt-4 overflow-x-auto pb-1">
                    <div class="flex min-w-max gap-3">
                        @foreach ($product['gallery'] as $index => $image)
                            <button
                                type="button"
                                class="overflow-hidden rounded-lg border-2 border-transparent transition"
                                :class="{ 'border-white': galleryIndex === {{ $index }} }"
                                @click="galleryIndex = {{ $index }}"
                            >
                                <img src="{{ $image }}" alt="Gallery thumbnail" class="h-16 w-24 object-cover">
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
