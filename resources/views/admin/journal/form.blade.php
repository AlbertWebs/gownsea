@php
    $imageUrl = $post->image ? (str_starts_with($post->image, 'http') ? $post->image : asset(ltrim($post->image, '/'))) : '';
@endphp
@extends('layouts.admin')
@section('title', $post->exists ? 'Edit article' : 'New article')
@section('content')
    <form method="POST" enctype="multipart/form-data" action="{{ $post->exists ? route('admin.journal.update', $post) : route('admin.journal.store') }}" class="space-y-6">
        @csrf @if($post->exists) @method('PUT') @endif
        <x-admin.form-header crumb="Dashboard / Content / {{ $post->exists ? 'Edit article' : 'New article' }}" :title="$post->exists ? 'Edit article' : 'Write an article'" description="Create a draft or publish it to The Gown Journal." :cancel="route('admin.journal.index')" :submit="$post->exists ? 'Save changes' : 'Create article'" />

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-6">
                <section class="admin-card space-y-4">
                    <h2>Article content</h2>
                    <label class="block text-sm font-semibold">Title <span class="text-[#d42127]">*</span><input class="admin-input mt-2" name="title" value="{{ old('title', $post->title) }}" required maxlength="190"></label>
                    <label class="block text-sm font-semibold">URL slug<input class="admin-input mt-2" name="slug" value="{{ old('slug', $post->slug) }}" placeholder="Generated from the title if blank"><span class="mt-1 block text-xs font-normal text-zinc-500">Lowercase letters, numbers, and hyphens.</span></label>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block text-sm font-semibold">Category<input class="admin-input mt-2" name="category" value="{{ old('category', $post->category) }}" placeholder="Graduation Attire"></label>
                        <label class="block text-sm font-semibold">Short summary<textarea class="admin-input mt-2" name="excerpt" rows="3" maxlength="600" required>{{ old('excerpt', $post->excerpt) }}</textarea></label>
                    </div>
                    <div><p class="text-sm font-semibold">Article body</p><div class="mt-2"><x-admin.editor name="body" :value="old('body', $post->body)" /></div></div>
                </section>
                <section class="admin-card space-y-4">
                    <div><h2>Cover image</h2><p class="mt-1 text-sm text-zinc-500">Optional. JPG, PNG, or WEBP up to 4MB.</p></div>
                    @if($imageUrl)<img src="{{ $imageUrl }}" alt="Article cover" class="max-h-64 rounded-xl object-cover">@endif
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="admin-input">
                    @if($imageUrl)<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remove_image" value="1"> Remove current cover</label>@endif
                </section>
                <section class="admin-card space-y-4">
                    <h2>Search preview</h2>
                    <label class="block text-sm font-semibold">SEO title<input class="admin-input mt-2" name="seo_title" value="{{ old('seo_title', $post->seo_title) }}"></label>
                    <label class="block text-sm font-semibold">SEO description<textarea class="admin-input mt-2" name="seo_description" rows="3">{{ old('seo_description', $post->seo_description) }}</textarea></label>
                </section>
            </div>
            <aside class="admin-card space-y-4 lg:sticky lg:top-24 self-start">
                <h2>Publishing</h2>
                <label class="block text-sm font-semibold">Status<select name="status" class="admin-input mt-2"><option value="draft" @selected(old('status', $post->status)==='draft')>Draft</option><option value="published" @selected(old('status', $post->status)==='published')>Published</option></select></label>
                <label class="block text-sm font-semibold">Publish date<input type="date" name="published_at" class="admin-input mt-2" value="{{ old('published_at', $post->published_at?->format('Y-m-d')) }}"><span class="mt-1 block text-xs font-normal text-zinc-500">Leave blank to use today when publishing.</span></label>
                @if($post->exists && $post->status === 'published')<a class="text-sm font-semibold text-[#d42127] underline" href="{{ route('journal.show', $post->slug) }}" target="_blank" rel="noopener">View live article</a>@endif
                <x-admin.btn class="w-full" icon="save">{{ $post->exists ? 'Save changes' : 'Create article' }}</x-admin.btn>
            </aside>
        </div>
    </form>
@endsection
