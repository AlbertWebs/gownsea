@extends('layouts.admin')
@section('title', $category->exists ? 'Edit category' : 'Add category')
@section('content')
    <h1>{{ $category->exists ? 'Edit category' : 'Add category' }}</h1>
    <form class="admin-card mt-6 max-w-xl space-y-4" method="POST" action="{{ $category->exists ? route('admin.catalogue.categories.update', $category) : route('admin.catalogue.categories.store') }}">
        @csrf
        @if($category->exists) @method('PUT') @endif
        <label class="block text-sm font-semibold">Name *<input class="admin-input mt-2" name="name" value="{{ old('name', $category->name) }}" required></label>
        <label class="block text-sm font-semibold">Slug<input class="admin-input mt-2" name="slug" value="{{ old('slug', $category->slug) }}"></label>
        <label class="block text-sm font-semibold">Parent
            <select class="admin-input mt-2" name="parent_id">
                <option value="">None</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id)==$parent->id)>{{ $parent->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-semibold">Description<textarea class="admin-input mt-2" name="description" rows="3">{{ old('description', $category->description) }}</textarea></label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))> Active</label>
        <button class="btn-primary">Save</button>
    </form>
@endsection
