@extends('layouts.admin')
@section('title', 'Products')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-zinc-500">Dashboard / Catalogue / Products</p>
            <h1>Products</h1>
            <p class="mt-1 text-sm text-zinc-600">Manage the same catalogue shown on the public website.</p>
        </div>
        <a class="btn-primary" href="{{ route('admin.catalogue.products.create') }}">Add Product</a>
    </div>
    <form class="mt-6 grid gap-3 md:grid-cols-6" method="GET">
        <input class="admin-input md:col-span-2" name="q" value="{{ request('q') }}" placeholder="Search products...">
        <select class="admin-input" name="category_id">
            <option value="">Category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select class="admin-input" name="status">
            <option value="">Status</option>
            @foreach (['draft','published','archived'] as $status)
                <option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>
            @endforeach
        </select>
        <select class="admin-input" name="visibility">
            <option value="">Visibility</option>
            <option value="public" @selected(request('visibility')==='public')>public</option>
            <option value="hidden" @selected(request('visibility')==='hidden')>hidden</option>
        </select>
        <button class="btn-secondary">Filter</button>
    </form>
    <div class="mt-6 overflow-x-auto rounded-2xl border border-zinc-200 bg-white">
        <table class="admin-table min-w-[900px]">
            <thead>
                <tr>
                    <th>Image</th><th>Product</th><th>SKU</th><th>Category</th><th>Price</th><th>Status</th><th>Inquiries</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>@if($product->image)<img src="{{ $product->image }}" class="h-12 w-12 rounded object-cover" alt="">@endif</td>
                        <td>
                            <a class="font-semibold" href="{{ route('admin.catalogue.products.show', $product) }}">{{ $product->name }}</a>
                            @if($product->featured)<span class="admin-badge">Featured</span>@endif
                        </td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ $product->category?->name }}</td>
                        <td>{{ $product->displayPrice() }}</td>
                        <td><span class="admin-badge">{{ $product->status }}</span></td>
                        <td>{{ $product->inquiries_count }}</td>
                        <td class="space-x-2 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.catalogue.products.edit', $product) }}">Edit</a>
                            <form class="inline" method="POST" action="{{ route('admin.catalogue.products.duplicate', $product) }}">@csrf<button>Duplicate</button></form>
                            <form class="inline" method="POST" action="{{ route('admin.catalogue.products.destroy', $product) }}" onsubmit="return confirm('Archive this product?')">@csrf @method('DELETE')<button class="text-[#d42127]">Delete</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-10 text-center text-zinc-500">No products have been added yet. <a class="text-[#d42127]" href="{{ route('admin.catalogue.products.create') }}">Add Product</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
@endsection
