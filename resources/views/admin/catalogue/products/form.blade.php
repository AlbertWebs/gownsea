@php
    $optionRows = collect(old('option_labels', array_keys($product->options ?? [])))
        ->values()
        ->map(function ($label, $index) use ($product) {
            $values = old('option_values.'.$index);
            if ($values === null) {
                $existing = $product->options[$label] ?? [];
                $values = is_array($existing) ? implode(', ', $existing) : '';
            }

            return ['label' => $label, 'values' => $values];
        });
    if ($optionRows->isEmpty()) {
        $optionRows = collect([['label' => 'Size', 'values' => 'Small, Medium, Large, X-Large']]);
    }

    $sizeRows = collect(old('size_labels', collect($product->size_guide ?? [])->pluck('size')->all()))
        ->values()
        ->map(function ($size, $index) use ($product) {
            return [
                'size' => $size,
                'guide' => old('size_guides.'.$index, $product->size_guide[$index]['guide'] ?? ''),
            ];
        });
    if ($sizeRows->isEmpty()) {
        $sizeRows = collect([['size' => '', 'guide' => '']]);
    }
@endphp

@extends('layouts.admin')
@section('title', $product->exists ? 'Edit product' : 'Add product')
@section('content')
    <form
        class="space-y-6"
        method="POST"
        enctype="multipart/form-data"
        action="{{ $product->exists ? route('admin.catalogue.products.update', $product) : route('admin.catalogue.products.store') }}"
        x-data="{
            tab: 'details',
            name: {{ \Illuminate\Support\Js::from(old('name', $product->name)) }},
            slug: {{ \Illuminate\Support\Js::from(old('slug', $product->slug)) }},
            slugLocked: {{ $product->exists ? 'true' : 'false' }},
            options: {{ \Illuminate\Support\Js::from($optionRows->values()) }},
            sizes: {{ \Illuminate\Support\Js::from($sizeRows->values()) }},
            slugify(value) {
                return String(value || '').toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            }
        }"
    >
        @csrf
        @if($product->exists) @method('PUT') @endif

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs text-zinc-500">Dashboard / Catalogue / Products / {{ $product->exists ? 'Edit' : 'Create' }}</p>
                <h1>{{ $product->exists ? 'Edit product' : 'Add product' }}</h1>
                <p class="mt-1 text-sm text-zinc-600">This catalogue item appears on the public website when it is published and visible.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-admin.btn :href="route('admin.catalogue.products.index')" variant="ghost" icon="x">Cancel</x-admin.btn>
                <x-admin.btn icon="save">{{ $product->exists ? 'Save changes' : 'Create product' }}</x-admin.btn>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 border-b border-zinc-200 pb-2 text-sm">
            @foreach (['details' => 'Details', 'pricing' => 'Pricing & stock', 'media' => 'Images', 'options' => 'Options & fit', 'seo' => 'SEO'] as $key => $label)
                <button type="button" class="rounded-full px-4 py-2" :class="tab === '{{ $key }}' ? 'bg-[#0f2744] text-white' : 'bg-zinc-100 text-zinc-700'" @click="tab = '{{ $key }}'">{{ $label }}</button>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-6">
                <section class="admin-card space-y-4" x-show="tab === 'details'">
                    <div>
                        <h2>Product details</h2>
                        <p class="mt-1 text-sm text-zinc-500">Core information shoppers see on the product page.</p>
                    </div>
                    <label class="block text-sm font-semibold">Product name <span class="text-[#d42127]">*</span>
                        <input class="admin-input mt-2" name="name" x-model="name" @input="if (!slugLocked) slug = slugify(name)" required>
                        @error('name')<span class="mt-1 block text-xs text-[#d42127]">{{ $message }}</span>@enderror
                    </label>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm font-semibold">Slug
                            <input class="admin-input mt-2" name="slug" x-model="slug" @input="slugLocked = true">
                            <span class="mt-1 block text-xs font-normal text-zinc-500">Generated from the name. Edit to lock it.</span>
                        </label>
                        <label class="block text-sm font-semibold">SKU
                            <input class="admin-input mt-2" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="GS-HOOD-001">
                        </label>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm font-semibold">Category
                            <select class="admin-input mt-2" name="category_id">
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id)==$category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block text-sm font-semibold">Brand
                            <input class="admin-input mt-2" name="brand" value="{{ old('brand', $product->brand ?? 'Gownsea') }}">
                        </label>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm font-semibold">Location
                            <input class="admin-input mt-2" name="location" value="{{ old('location', $product->location ?? 'Nairobi') }}">
                        </label>
                        <label class="block text-sm font-semibold">Public URL path
                            <input class="admin-input mt-2" name="url_path" value="{{ old('url_path', $product->url_path) }}" placeholder="/our-products/undergraduate-academic-hoods">
                        </label>
                    </div>
                    <label class="block text-sm font-semibold">Short description
                        <textarea class="admin-input mt-2" name="short_description" rows="3" placeholder="One or two sentences for cards and listings.">{{ old('short_description', $product->short_description) }}</textarea>
                    </label>
                    <div>
                        <p class="text-sm font-semibold">Full description</p>
                        <p class="mb-2 text-xs font-normal text-zinc-500">Shown in the About section on the product page.</p>
                        <x-admin.editor name="description" :value="old('description', $product->description)" />
                    </div>
                    <label class="block text-sm font-semibold">Product details <span class="font-normal text-zinc-500">(one bullet per line)</span>
                        <textarea class="admin-input mt-2" name="details_text" rows="5" placeholder="Traditional undergraduate academic hood">{{ old('details_text', collect($product->details ?? [])->implode("\n")) }}</textarea>
                    </label>
                    <label class="block text-sm font-semibold">Call to action label
                        <input class="admin-input mt-2" name="cta" value="{{ old('cta', $product->cta ?? 'Request Quote') }}">
                    </label>
                    <label class="block text-sm font-semibold">Tags
                        <input class="admin-input mt-2" name="tags_text" value="{{ old('tags_text', collect($product->tags ?? [])->implode(', ')) }}" placeholder="hoods, graduation, hire">
                    </label>
                </section>

                <section class="admin-card space-y-4" x-show="tab === 'pricing'">
                    <div>
                        <h2>Pricing & stock</h2>
                        <p class="mt-1 text-sm text-zinc-500">Use a display label for quotes, or a numeric amount for reports.</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm font-semibold">Display price
                            <input class="admin-input mt-2" name="price_label" value="{{ old('price_label', $product->price_label) }}" placeholder="KES 3,000 or Request quote">
                        </label>
                        <label class="block text-sm font-semibold">Price amount (KES)
                            <input class="admin-input mt-2" type="number" min="0" name="price_amount" value="{{ old('price_amount', $product->price_amount) }}">
                        </label>
                        <label class="block text-sm font-semibold">Sale price (KES)
                            <input class="admin-input mt-2" type="number" min="0" name="sale_price_amount" value="{{ old('sale_price_amount', $product->sale_price_amount) }}">
                        </label>
                        <label class="block text-sm font-semibold">Stock quantity
                            <input class="admin-input mt-2" type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}">
                        </label>
                        <label class="block text-sm font-semibold">Minimum order qty
                            <input class="admin-input mt-2" type="number" min="1" name="min_order_qty" value="{{ old('min_order_qty', $product->min_order_qty ?? 1) }}">
                        </label>
                        <label class="block text-sm font-semibold">Availability
                            <select class="admin-input mt-2" name="availability">
                                <option value="in_stock" @selected(old('availability', $product->availability)==='in_stock')>In stock</option>
                                <option value="made_to_order" @selected(old('availability', $product->availability)==='made_to_order')>Made to order</option>
                                <option value="out_of_stock" @selected(old('availability', $product->availability)==='out_of_stock')>Out of stock</option>
                            </select>
                        </label>
                    </div>
                </section>

                <section class="admin-card space-y-5" x-show="tab === 'media'">
                    <div>
                        <h2>Images</h2>
                        <p class="mt-1 text-sm text-zinc-500">Drop files or click to browse. Main image is used on cards and as the first gallery photo.</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Main image</p>
                        <div class="admin-dropzone mt-2" :class="hover ? 'is-hover' : ''" x-data="dropzone({ multiple: false })" @dragover.prevent="hover = true" @dragleave.prevent="hover = false" @drop.prevent="hover = false; addFiles($event.dataTransfer.files)">
                            <input x-ref="input" class="sr-only" type="file" name="image" accept="image/*" @change="addFromInput($event)">
                            <button type="button" class="admin-dropzone__hit" @click="$refs.input.click()">
                                <span class="admin-dropzone__title">Drop main image here</span>
                                <span class="admin-dropzone__hint">JPG, PNG, WEBP · max 4MB</span>
                            </button>
                            <div class="admin-dropzone__previews" x-show="urls.length">
                                <template x-for="(url, index) in urls" :key="url">
                                    <figure class="admin-dropzone__thumb"><img :src="url" alt=""><button type="button" @click="remove(index)">Remove</button></figure>
                                </template>
                            </div>
                            <p class="admin-dropzone__error" x-show="error" x-text="error"></p>
                        </div>
                        @if($product->image)
                            <p class="mt-2 text-xs text-zinc-500">Current image</p>
                            <img src="{{ $product->image }}" class="mt-1 h-24 rounded object-cover" alt="">
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Gallery</p>
                        <div class="admin-dropzone mt-2" :class="hover ? 'is-hover' : ''" x-data="dropzone({ multiple: true })" @dragover.prevent="hover = true" @dragleave.prevent="hover = false" @drop.prevent="hover = false; addFiles($event.dataTransfer.files)">
                            <input x-ref="input" class="sr-only" type="file" name="gallery[]" accept="image/*" multiple @change="addFromInput($event)">
                            <button type="button" class="admin-dropzone__hit" @click="$refs.input.click()">
                                <span class="admin-dropzone__title">Drop gallery images here</span>
                                <span class="admin-dropzone__hint">Add several photos for the product page</span>
                            </button>
                            <div class="admin-dropzone__previews" x-show="urls.length">
                                <template x-for="(url, index) in urls" :key="url">
                                    <figure class="admin-dropzone__thumb"><img :src="url" alt=""><button type="button" @click="remove(index)">Remove</button></figure>
                                </template>
                            </div>
                            <p class="admin-dropzone__error" x-show="error" x-text="error"></p>
                        </div>
                        @if($product->exists && $product->images->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($product->images as $image)
                                    <img src="{{ $image->path }}" class="h-16 w-16 rounded object-cover" alt="">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>

                <section class="admin-card space-y-4" x-show="tab === 'options'">
                    <div>
                        <h2>Options & fit</h2>
                        <p class="mt-1 text-sm text-zinc-500">These become the colour/size buttons on the public product page.</p>
                    </div>
                    <div class="space-y-3">
                        <template x-for="(option, index) in options" :key="index">
                            <div class="grid gap-3 md:grid-cols-[180px_1fr_auto]">
                                <input class="admin-input" :name="'option_labels['+index+']'" x-model="option.label" placeholder="Faculty colour">
                                <input class="admin-input" :name="'option_values['+index+']'" x-model="option.values" placeholder="Red, Navy, Gold">
                                <button type="button" class="text-sm text-[#d42127]" @click="options.splice(index, 1)">Remove</button>
                            </div>
                        </template>
                        <button type="button" class="text-sm font-semibold text-[#d42127]" @click="options.push({ label: '', values: '' })">Add option</button>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Size guide</p>
                        <div class="mt-3 space-y-3">
                            <template x-for="(row, index) in sizes" :key="index">
                                <div class="grid gap-3 md:grid-cols-[140px_1fr_auto]">
                                    <input class="admin-input" :name="'size_labels['+index+']'" x-model="row.size" placeholder="Medium">
                                    <input class="admin-input" :name="'size_guides['+index+']'" x-model="row.guide" placeholder="Standard undergraduate fit">
                                    <button type="button" class="text-sm text-[#d42127]" @click="sizes.splice(index, 1)">Remove</button>
                                </div>
                            </template>
                            <button type="button" class="text-sm font-semibold text-[#d42127]" @click="sizes.push({ size: '', guide: '' })">Add size row</button>
                        </div>
                    </div>
                    <label class="block text-sm font-semibold">Fit note
                        <textarea class="admin-input mt-2" name="fit_note" rows="3">{{ old('fit_note', $product->fit_note) }}</textarea>
                    </label>
                </section>

                <section class="admin-card space-y-4" x-show="tab === 'seo'">
                    <div>
                        <h2>SEO</h2>
                        <p class="mt-1 text-sm text-zinc-500">Leave blank to use the product name and short description.</p>
                    </div>
                    <label class="block text-sm font-semibold">SEO title
                        <input class="admin-input mt-2" name="seo_title" value="{{ old('seo_title', $product->seo_title) }}" maxlength="190">
                    </label>
                    <label class="block text-sm font-semibold">SEO description
                        <textarea class="admin-input mt-2" name="seo_description" rows="4" maxlength="500">{{ old('seo_description', $product->seo_description) }}</textarea>
                    </label>
                    <label class="block text-sm font-semibold">Keywords
                        <input class="admin-input mt-2" name="seo_keywords" value="{{ old('seo_keywords', $product->seo_keywords) }}" placeholder="graduation hoods, academic hoods Kenya">
                    </label>
                </section>
            </div>

            <aside class="space-y-4 lg:sticky lg:top-24 self-start">
                <div class="admin-card space-y-4">
                    <h2>Publish</h2>
                    <label class="block text-sm font-semibold">Status
                        <select class="admin-input mt-2" name="status">
                            @foreach (['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $product->status)===$value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block text-sm font-semibold">Visibility
                        <select class="admin-input mt-2" name="visibility">
                            <option value="public" @selected(old('visibility', $product->visibility)==='public')>Public on website</option>
                            <option value="hidden" @selected(old('visibility', $product->visibility)==='hidden')>Hidden</option>
                        </select>
                    </label>
                    <label class="flex items-start gap-2 text-sm"><input class="mt-1" type="checkbox" name="featured" value="1" @checked(old('featured', $product->featured))><span>Featured on homepage collections</span></label>
                    <label class="flex items-start gap-2 text-sm"><input class="mt-1" type="checkbox" name="is_hire" value="1" @checked(old('is_hire', $product->is_hire))><span>Available for hire</span></label>
                    <x-admin.btn class="w-full" icon="save">{{ $product->exists ? 'Save changes' : 'Create product' }}</x-admin.btn>
                </div>
            </aside>
        </div>
    </form>
@endsection
