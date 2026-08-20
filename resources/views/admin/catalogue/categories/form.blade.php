@php
    $previewPath = $category->previewImage();
    $previewUrl = $previewPath
        ? (str_starts_with($previewPath, 'http') ? $previewPath : asset(ltrim($previewPath, '/')))
        : '';
    $hasSavedImage = filled($category->image);
@endphp

@extends('layouts.admin')
@section('title', $category->exists ? 'Edit category' : 'Add category')
@section('content')
    <form class="space-y-6" method="POST" enctype="multipart/form-data" action="{{ $category->exists ? route('admin.catalogue.categories.update', $category) : route('admin.catalogue.categories.store') }}" x-data="{ tab: 'details' }">
        @csrf
        @if($category->exists) @method('PUT') @endif

        <x-admin.form-header
            crumb="Dashboard / Catalogue / Categories / {{ $category->exists ? 'Edit' : 'Create' }}"
            :title="$category->exists ? 'Edit category' : 'Add category'"
            description="Categories organise products on the public website."
            :cancel="route('admin.catalogue.categories.index')"
            :submit="$category->exists ? 'Save changes' : 'Create category'"
        />

        <div class="flex flex-wrap gap-2 border-b border-zinc-200 pb-2 text-sm">
            <button type="button" class="rounded-2xl px-4 py-2" :class="tab === 'details' ? 'bg-[#0f2744] text-white' : 'bg-zinc-100 text-zinc-700'" @click="tab = 'details'">Details</button>
            <button type="button" class="rounded-2xl px-4 py-2" :class="tab === 'images' ? 'bg-[#0f2744] text-white' : 'bg-zinc-100 text-zinc-700'" @click="tab = 'images'">Images</button>
            <button type="button" class="rounded-2xl px-4 py-2" :class="tab === 'seo' ? 'bg-[#0f2744] text-white' : 'bg-zinc-100 text-zinc-700'" @click="tab = 'seo'">SEO</button>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-6">
                <section class="admin-card space-y-4" x-show="tab === 'details'">
                    <div>
                        <h2>Category details</h2>
                        <p class="mt-1 text-sm text-zinc-500">Name and placement in the catalogue.</p>
                    </div>
                    <label class="block text-sm font-semibold">Name <span class="text-[#d42127]">*</span>
                        <input class="admin-input mt-2" name="name" value="{{ old('name', $category->name) }}" required>
                    </label>
                    <label class="block text-sm font-semibold">Slug
                        <input class="admin-input mt-2" name="slug" value="{{ old('slug', $category->slug) }}">
                        <span class="mt-1 block text-xs font-normal text-zinc-500">Leave blank to generate from the name.</span>
                    </label>
                    <label class="block text-sm font-semibold">Parent category
                        <select class="admin-input mt-2" name="parent_id">
                            <option value="">None</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id)==$parent->id)>{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div>
                        <p class="text-sm font-semibold">Description</p>
                        <div class="mt-2">
                            <x-admin.editor name="description" :value="old('description', $category->description)" />
                        </div>
                    </div>
                    <label class="block text-sm font-semibold">Display order
                        <input class="admin-input mt-2" type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                    </label>
                </section>
                <section
                    class="admin-card space-y-5"
                    x-show="tab === 'images'"
                    x-data="{
                        preview: {{ \Illuminate\Support\Js::from($previewUrl) }},
                        hover: false,
                        error: '',
                        setFiles(fileList) {
                            this.error = '';
                            const file = Array.from(fileList || []).find((item) => item.type.startsWith('image/'));
                            if (! file) {
                                this.error = 'Please choose an image file.';
                                return;
                            }
                            if (file.size > 4 * 1024 * 1024) {
                                this.error = 'Each image must be 4MB or smaller.';
                                return;
                            }
                            this.preview = URL.createObjectURL(file);
                            const transfer = new DataTransfer();
                            transfer.items.add(file);
                            this.$refs.input.files = transfer.files;
                        }
                    }"
                >
                    <div>
                        <h2>Category image</h2>
                        <p class="mt-1 text-sm text-zinc-500">Used as the category cover on collection pages. JPG, PNG or WEBP, up to 4MB.</p>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100">
                        <img
                            x-show="preview"
                            x-cloak
                            :src="preview"
                            alt="{{ $category->name }} image preview"
                            class="mx-auto max-h-72 w-full object-contain"
                        >
                        <p x-show="!preview" class="px-4 py-16 text-center text-sm text-zinc-400">No image to preview yet.</p>
                    </div>
                    @if ($previewUrl && ! $hasSavedImage)
                        <p class="text-xs text-zinc-500">Showing the current public cover. Save an upload here to replace it for this category.</p>
                    @elseif ($hasSavedImage)
                        <p class="text-xs text-zinc-500">Current saved category image.</p>
                    @endif

                    <div class="admin-dropzone" :class="hover ? 'is-hover' : ''" @dragover.prevent="hover = true" @dragleave.prevent="hover = false" @drop.prevent="hover = false; setFiles($event.dataTransfer.files)">
                        <input x-ref="input" class="sr-only" type="file" name="image" accept="image/*" @change="setFiles($event.target.files)">
                        <button type="button" class="admin-dropzone__hit" @click="$refs.input.click()">
                            <span class="admin-dropzone__title">Drop a new image here</span>
                            <span class="admin-dropzone__hint">Or click to browse</span>
                        </button>
                        <p class="admin-dropzone__error" x-show="error" x-text="error"></p>
                    </div>
                    @if($hasSavedImage)
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-zinc-700">
                            <input type="checkbox" name="remove_image" value="1">
                            Remove current image
                        </label>
                    @endif
                    @error('image')<span class="block text-xs text-[#d42127]">{{ $message }}</span>@enderror
                </section>
                <section class="admin-card space-y-4" x-show="tab === 'seo'">
                    <div>
                        <h2>SEO</h2>
                        <p class="mt-1 text-sm text-zinc-500">Optional overrides for category pages.</p>
                    </div>
                    <label class="block text-sm font-semibold">SEO title
                        <input class="admin-input mt-2" name="seo_title" value="{{ old('seo_title', $category->seo_title) }}">
                    </label>
                    <label class="block text-sm font-semibold">SEO description
                        <textarea class="admin-input mt-2" name="seo_description" rows="4">{{ old('seo_description', $category->seo_description) }}</textarea>
                    </label>
                </section>
            </div>
            <aside class="admin-card space-y-4 lg:sticky lg:top-24 self-start">
                <h2>Publish</h2>
                @if ($previewUrl)
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Current image</p>
                        <img src="{{ $previewUrl }}" alt="{{ $category->name }}" class="mt-2 h-28 w-full rounded-xl object-cover">
                    </div>
                @endif
                <label class="flex items-start gap-2 text-sm"><input class="mt-1" type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))><span>Active on the website</span></label>
                <x-admin.btn class="w-full" icon="save">{{ $category->exists ? 'Save changes' : 'Create category' }}</x-admin.btn>
            </aside>
        </div>
    </form>
@endsection
