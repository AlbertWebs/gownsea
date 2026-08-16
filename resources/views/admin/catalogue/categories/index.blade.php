@extends('layouts.admin')
@section('title', 'Categories')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-zinc-500">Dashboard / Catalogue / Categories</p>
            <h1>Categories</h1>
            <p class="mt-1 text-sm text-zinc-600">Organise the public catalogue. Nested categories appear indented under their parent.</p>
        </div>
        <x-admin.btn :href="route('admin.catalogue.categories.create')" icon="plus">Add category</x-admin.btn>
    </div>

    <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="admin-card py-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Total</p>
            <p class="mt-1 text-2xl font-semibold">{{ $stats['total'] }}</p>
        </article>
        <article class="admin-card py-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Active</p>
            <p class="mt-1 text-2xl font-semibold">{{ $stats['active'] }}</p>
        </article>
        <article class="admin-card py-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Inactive</p>
            <p class="mt-1 text-2xl font-semibold">{{ $stats['inactive'] }}</p>
        </article>
        <article class="admin-card py-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">With products</p>
            <p class="mt-1 text-2xl font-semibold">{{ $stats['with_products'] }}</p>
        </article>
    </div>

    <form class="mt-6 flex flex-nowrap items-center gap-3" method="GET">
        <input class="admin-input min-w-0 flex-1 !w-auto" name="q" value="{{ request('q') }}" placeholder="Search by name or slug...">
        <select class="admin-input w-44 shrink-0 !w-44" name="status">
            <option value="">All statuses</option>
            <option value="active" @selected(request('status')==='active')>Active</option>
            <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
        </select>
        <div class="flex shrink-0 items-center gap-2">
            <x-admin.btn variant="violet" icon="filter">Filter</x-admin.btn>
            @if(request()->hasAny(['q', 'status']))
                <x-admin.btn :href="route('admin.catalogue.categories.index')" variant="ghost" icon="x">Clear</x-admin.btn>
            @endif
        </div>
    </form>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="hidden md:table-cell">Parent</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($categories as $category)
                <tr class="hover:bg-zinc-50">
                    <td>
                        <div class="flex items-start gap-2" style="padding-left: {{ ($category->depth ?? 0) * 1.25 }}rem">
                            @if(($category->depth ?? 0) > 0)
                                <span class="mt-1 text-zinc-300" aria-hidden="true">└</span>
                            @endif
                            <div>
                                <a class="font-semibold text-zinc-900 hover:text-[#d42127]" href="{{ route('admin.catalogue.categories.edit', $category) }}">{{ $category->name }}</a>
                                <p class="mt-0.5 font-mono text-xs text-zinc-500">/{{ $category->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="hidden text-zinc-600 md:table-cell">{{ $category->parent?->name ?? '—' }}</td>
                    <td>
                        @if($category->products_count)
                            <a class="font-semibold text-[#0f2744] hover:text-[#d42127]" href="{{ route('admin.catalogue.products.index', ['category_id' => $category->id]) }}">{{ $category->products_count }}</a>
                        @else
                            <span class="text-zinc-400">0</span>
                        @endif
                    </td>
                    <td>
                        <span class="admin-badge {{ $category->is_active ? 'admin-badge--ok' : '' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                            <a class="btn-navy btn-sm" href="{{ route('admin.catalogue.categories.edit', $category) }}"><x-admin.icon name="edit" /> Edit</a>
                            @if($category->products_count)
                                <button type="button" class="btn-danger btn-sm" disabled title="Reassign products before deleting"><x-admin.icon name="trash" /> Delete</button>
                            @else
                                <form method="POST" action="{{ route('admin.catalogue.categories.destroy', $category) }}" onsubmit="return confirm('Delete {{ addslashes($category->name) }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm"><x-admin.icon name="trash" /> Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="admin-table__empty">
                        <p class="text-base font-semibold text-zinc-800">No categories match</p>
                        <p class="mt-1 text-sm text-zinc-500">
                            @if(request()->hasAny(['q', 'status']))
                                Try clearing filters, or <a class="font-semibold text-[#d42127]" href="{{ route('admin.catalogue.categories.create') }}">add a category</a>.
                            @else
                                Start by <a class="font-semibold text-[#d42127]" href="{{ route('admin.catalogue.categories.create') }}">adding a category</a> for the public catalogue.
                            @endif
                        </p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
