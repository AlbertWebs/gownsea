@extends('layouts.public')

@section('content')
    <section
        class="container-shell section-lg"
        x-data="{
            query: '',
            category: 'all',
            location: 'all',
            price: 'all',
            quickView: null,
            matches(property) {
                const searchMatch = this.query === '' || property.title.toLowerCase().includes(this.query.toLowerCase()) || property.description.toLowerCase().includes(this.query.toLowerCase());
                const categoryMatch = this.category === 'all' || property.category === this.category;
                const locationMatch = this.location === 'all' || property.location.toLowerCase().includes(this.location.toLowerCase());
                const numericPrice = Number((property.price || '').replace(/[^0-9]/g, ''));
                const priceMatch = this.price === 'all'
                    || (this.price === 'low' && numericPrice <= 10000)
                    || (this.price === 'mid' && numericPrice > 10000 && numericPrice <= 20000)
                    || (this.price === 'high' && numericPrice > 20000);
                return searchMatch && categoryMatch && locationMatch && priceMatch;
            }
        }"
        @quick-view-open.window="quickView = $event.detail.property"
    >
        <x-ui.section-header
            kicker="Collections"
            title="Browse available attire and choose the right fit"
            description="Use filters to quickly find options by type, location, and budget range."
        />

        <div class="surface mt-8 p-4 md:p-5">
            <div class="luxury-grid md:grid-cols-4">
                <label class="text-xs font-semibold text-zinc-700">
                    Search
                    <input x-model="query" type="text" placeholder="Search collection" class="mt-2 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm">
                </label>

                <label class="text-xs font-semibold text-zinc-700">
                    Category
                    <select x-model="category" class="mt-2 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm">
                        <option value="all">All categories</option>
                        <option value="graduation">Graduation</option>
                        <option value="legal">Legal</option>
                        <option value="church">Church</option>
                    </select>
                </label>

                <label class="text-xs font-semibold text-zinc-700">
                    Location
                    <select x-model="location" class="mt-2 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm">
                        <option value="all">All locations</option>
                        <option value="nairobi">Nairobi</option>
                        <option value="nationwide">Nationwide</option>
                    </select>
                </label>

                <label class="text-xs font-semibold text-zinc-700">
                    Price range
                    <select x-model="price" class="mt-2 w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm">
                        <option value="all">All prices</option>
                        <option value="low">Up to KES 10,000</option>
                        <option value="mid">KES 10,001 - 20,000</option>
                        <option value="high">Above KES 20,000</option>
                    </select>
                </label>
            </div>
        </div>

        <div class="luxury-grid mt-8 md:grid-cols-3">
            @foreach ($properties as $property)
                <div x-show="matches(@js($property))" x-transition.opacity.scale.95>
                    <x-ui.property-card :property="$property" :showQuickView="true" />
                </div>
            @endforeach
        </div>

        <div
            x-show="quickView"
            x-transition.opacity
            x-cloak
            class="fixed inset-0 z-[70] flex items-center justify-center bg-zinc-950/55 px-4"
            @keydown.escape.window="quickView = null"
        >
            <div class="surface w-full max-w-2xl overflow-hidden" @click.away="quickView = null">
                <template x-if="quickView">
                    <div>
                        <div class="relative aspect-[16/9] overflow-hidden bg-zinc-200">
                            <img
                                :src="quickView.image || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=70'"
                                :alt="quickView.title"
                                width="1200"
                                height="675"
                                class="h-full w-full object-cover"
                                x-on:error="$el.src='https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=70'"
                            >
                        </div>
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="kicker" x-text="quickView.category || 'Collection'"></p>
                                    <h3 class="mt-2 font-semibold" x-text="quickView.title"></h3>
                                </div>
                                <button type="button" class="rounded-md border border-zinc-300 px-2 py-1 text-xs" @click="quickView = null">Close</button>
                            </div>
                            <p class="mt-4 text-sm text-zinc-600" x-text="quickView.description"></p>
                            <div class="mt-4 flex items-center justify-between text-sm">
                                <span class="font-semibold text-[#d42127]" x-text="quickView.price"></span>
                                <span class="text-zinc-500" x-text="quickView.location"></span>
                            </div>
                            <a :href="quickView.url || '/shop-attire/graduation-attire'" class="btn-primary mt-5">Open detail page</a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>
@endsection
