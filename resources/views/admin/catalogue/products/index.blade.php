@extends('layouts.admin')
@section('title', 'Products')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-zinc-500">Dashboard / Catalogue / Products</p>
            <h1>Products</h1>
            <p class="mt-1 text-sm text-zinc-600">Manage the same catalogue shown on the public website.</p>
        </div>
        <x-admin.btn :href="route('admin.catalogue.products.create')" icon="plus">Add Product</x-admin.btn>
    </div>
    <form class="mt-6 flex flex-nowrap items-center gap-3" method="GET">
        <input class="admin-input min-w-0 flex-1 !w-auto" name="q" value="{{ request('q') }}" placeholder="Search products...">
        <select class="admin-input w-40 shrink-0 !w-40" name="category_id">
            <option value="">Category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select class="admin-input w-36 shrink-0 !w-36" name="status">
            <option value="">Status</option>
            @foreach (['draft','published','archived'] as $status)
                <option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>
            @endforeach
        </select>
        <select class="admin-input w-36 shrink-0 !w-36" name="visibility">
            <option value="">Visibility</option>
            <option value="public" @selected(request('visibility')==='public')>public</option>
            <option value="hidden" @selected(request('visibility')==='hidden')>hidden</option>
        </select>
        <x-admin.btn class="shrink-0" variant="violet" icon="filter">Filter</x-admin.btn>
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table min-w-[960px]">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Inquiries</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            @if($product->image)
                                <img src="{{ $product->image }}" class="h-12 w-12 rounded-lg object-cover" alt="">
                            @else
                                <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-zinc-100 text-xs text-zinc-400">—</span>
                            @endif
                        </td>
                        <td>
                            <a class="admin-table__name" href="{{ route('admin.catalogue.products.show', $product) }}">{{ $product->name }}</a>
                            @if($product->featured)<div class="mt-1"><x-admin.badge status="featured" /></div>@endif
                        </td>
                        <td class="font-mono text-xs text-zinc-500">{{ $product->sku ?: '—' }}</td>
                        <td>{{ $product->category?->name ?? '—' }}</td>
                        <td class="font-semibold text-zinc-900">{{ $product->displayPrice() }}</td>
                        <td><x-admin.badge :status="$product->status" /></td>
                        <td>{{ $product->inquiries_count ?: '—' }}</td>
                        <td>
                            <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                <a class="btn-navy btn-sm" href="{{ route('admin.catalogue.products.edit', $product) }}"><x-admin.icon name="edit" /> Edit</a>
                                <form method="POST" action="{{ route('admin.catalogue.products.duplicate', $product) }}">
                                    @csrf
                                    <button type="submit" class="btn-teal btn-sm"><x-admin.icon name="copy" /> Duplicate</button>
                                </form>
                                <form method="POST" action="{{ route('admin.catalogue.products.destroy', $product) }}" onsubmit="return confirm('Archive this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm"><x-admin.icon name="trash" /> Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="admin-table__empty">
                            <p>No products yet</p>
                            <p><a class="font-semibold text-[#d42127]" href="{{ route('admin.catalogue.products.create') }}">Add a product</a> to the public catalogue.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
@endsection
