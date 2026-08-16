@extends('layouts.admin')
@section('title', 'Categories')
@section('content')
    <div class="flex items-end justify-between">
        <div><h1>Categories</h1><p class="text-sm text-zinc-600">Organise the public catalogue.</p></div>
        <a class="btn-primary" href="{{ route('admin.catalogue.categories.create') }}">Add category</a>
    </div>
    <div class="mt-6 overflow-x-auto rounded-2xl border border-zinc-200 bg-white">
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->products_count }}</td>
                    <td>{{ $category->is_active ? 'Active' : 'Inactive' }}</td>
                    <td class="space-x-2">
                        <a href="{{ route('admin.catalogue.categories.edit', $category) }}">Edit</a>
                        <form class="inline" method="POST" action="{{ route('admin.catalogue.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">@csrf @method('DELETE')<button class="text-[#d42127]">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-zinc-500">No categories yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
