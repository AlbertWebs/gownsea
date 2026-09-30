@extends('layouts.admin')
@section('title', 'Journal Image Library')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-zinc-500">Dashboard / Content / Blogs &amp; Articles / Images</p>
            <h1>Journal image library</h1>
            <p class="mt-1 text-sm text-zinc-600">Upload images once, preview them here, then reuse them as article covers.</p>
        </div>
        <x-admin.btn :href="route('admin.journal.index')" variant="ghost">Back to articles</x-admin.btn>
    </div>

    <form class="admin-card mt-6 space-y-4" method="POST" enctype="multipart/form-data" action="{{ route('admin.journal.images.upload') }}">
        @csrf
        <div>
            <h2>Upload images</h2>
            <p class="mt-1 text-sm text-zinc-500">Drop several JPG, PNG, or WEBP images here, or browse your device. Up to 12 images per upload, 4MB each.</p>
        </div>
        <div class="admin-dropzone" :class="hover ? 'is-hover' : ''" x-data="dropzone({ multiple: true })" @dragover.prevent="hover = true" @dragleave.prevent="hover = false" @drop.prevent="hover = false; addFiles($event.dataTransfer.files)">
            <input x-ref="input" class="sr-only" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple @change="addFromInput($event)">
            <button type="button" class="admin-dropzone__hit" @click="$refs.input.click()">
                <span class="admin-dropzone__title">Drop your images here</span>
                <span class="admin-dropzone__hint">Or click to browse · JPG, PNG, WEBP · up to 12 files</span>
            </button>
            <div class="admin-dropzone__previews" x-show="urls.length">
                <template x-for="(url, index) in urls" :key="url">
                    <figure class="admin-dropzone__thumb"><img :src="url" alt="Image upload preview"><button type="button" @click="remove(index)">Remove</button></figure>
                </template>
            </div>
            <p class="admin-dropzone__error" x-show="error" x-text="error"></p>
        </div>
        <x-admin.btn icon="upload">Upload selected images</x-admin.btn>
    </form>

    <section class="mt-8">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <div><h2>Uploaded images</h2><p class="mt-1 text-sm text-zinc-500">{{ count($images) }} image{{ count($images) === 1 ? '' : 's' }} available to reuse.</p></div>
        </div>
        @if(count($images))
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($images as $image)
                    <article class="admin-card overflow-hidden p-0">
                        <a href="{{ asset(ltrim($image, '/')) }}" target="_blank" rel="noopener" aria-label="Open {{ basename($image) }} at full size">
                            <img src="{{ asset(ltrim($image, '/')) }}" alt="{{ basename($image) }}" loading="lazy" class="aspect-[4/3] w-full bg-zinc-100 object-cover transition hover:opacity-90">
                        </a>
                        <div class="p-4">
                            <p class="truncate text-sm font-semibold text-zinc-800" title="{{ basename($image) }}">{{ basename($image) }}</p>
                            <a class="mt-2 inline-flex text-sm font-semibold text-[#d42127] underline" href="{{ asset(ltrim($image, '/')) }}" target="_blank" rel="noopener">View full image</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="admin-card py-12 text-center">
                <p class="text-base font-semibold text-zinc-800">Your image library is empty</p>
                <p class="mt-1 text-sm text-zinc-500">Upload images above to use them in Gown Journal articles.</p>
            </div>
        @endif
    </section>
@endsection
