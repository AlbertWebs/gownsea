@extends('layouts.admin')
@section('title', $product->exists ? 'Edit product' : 'Add product')
@section('content')
    <p class="text-xs text-zinc-500">Dashboard / Catalogue / Products / {{ $product->exists ? 'Edit' : 'Create' }}</p>
    <h1>{{ $product->exists ? 'Edit product' : 'Add product' }}</h1>
    <form class="mt-6 grid gap-6 lg:grid-cols-3" method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.catalogue.products.update', $product) : route('admin.catalogue.products.store') }}">
        @csrf
        @if($product->exists) @method('PUT') @endif
        <div class="admin-card space-y-4 lg:col-span-2">
            <label class="block text-sm font-semibold">Product name *<input class="admin-input mt-2" name="name" value="{{ old('name', $product->name) }}" required></label>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="block text-sm font-semibold">Slug<input class="admin-input mt-2" name="slug" value="{{ old('slug', $product->slug) }}"></label>
                <label class="block text-sm font-semibold">SKU<input class="admin-input mt-2" name="sku" value="{{ old('sku', $product->sku) }}"></label>
            </div>
            <label class="block text-sm font-semibold">Category
                <select class="admin-input mt-2" name="category_id">
                    <option value="">None</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id)==$category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm font-semibold">Short description<textarea class="admin-input mt-2" name="short_description" rows="3">{{ old('short_description', $product->short_description) }}</textarea></label>
            <label class="block text-sm font-semibold">Full description<textarea class="admin-input mt-2" name="description" rows="6">{{ old('description', $product->description) }}</textarea></label>
            <label class="block text-sm font-semibold">Details (one per line)<textarea class="admin-input mt-2" name="details_text" rows="5">{{ old('details_text', collect($product->details ?? [])->implode("\n")) }}</textarea></label>
            <label class="block text-sm font-semibold">Fit note<textarea class="admin-input mt-2" name="fit_note" rows="2">{{ old('fit_note', $product->fit_note) }}</textarea></label>
        </div>
        <div class="space-y-4">
            <div class="admin-card space-y-4">
                <label class="block text-sm font-semibold">Price label<input class="admin-input mt-2" name="price_label" value="{{ old('price_label', $product->price_label) }}" placeholder="KES 3,000"></label>
                <label class="block text-sm font-semibold">Price amount<input class="admin-input mt-2" type="number" name="price_amount" value="{{ old('price_amount', $product->price_amount) }}"></label>
                <label class="block text-sm font-semibold">Status
                    <select class="admin-input mt-2" name="status">
                        @foreach (['draft','published','archived'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $product->status)===$status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block text-sm font-semibold">Visibility
                    <select class="admin-input mt-2" name="visibility">
                        <option value="public" @selected(old('visibility', $product->visibility)==='public')>public</option>
                        <option value="hidden" @selected(old('visibility', $product->visibility)==='hidden')>hidden</option>
                    </select>
                </label>
                <label class="block text-sm font-semibold">Availability
                    <select class="admin-input mt-2" name="availability">
                        @foreach (['in_stock','made_to_order','out_of_stock'] as $availability)
                            <option value="{{ $availability }}" @selected(old('availability', $product->availability)===$availability)>{{ $availability }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="featured" value="1" @checked(old('featured', $product->featured))> Featured</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_hire" value="1" @checked(old('is_hire', $product->is_hire))> Available for hire</label>
            </div>
            <div class="admin-card space-y-4">
                <label class="block text-sm font-semibold">Main image<input class="admin-input mt-2" type="file" name="image" accept="image/*"></label>
                @if($product->image)<img src="{{ $product->image }}" class="mt-2 h-24 rounded object-cover" alt="">@endif
                <label class="block text-sm font-semibold">Gallery<input class="admin-input mt-2" type="file" name="gallery[]" accept="image/*" multiple></label>
                <label class="block text-sm font-semibold">SEO title<input class="admin-input mt-2" name="seo_title" value="{{ old('seo_title', $product->seo_title) }}"></label>
                <label class="block text-sm font-semibold">SEO description<textarea class="admin-input mt-2" name="seo_description" rows="3">{{ old('seo_description', $product->seo_description) }}</textarea></label>
            </div>
            <button class="btn-primary w-full">Save product</button>
        </div>
    </form>
@endsection
