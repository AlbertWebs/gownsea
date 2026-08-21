@extends('layouts.admin')
@section('title', 'Hero carousel')
@section('content')
    @php
        $initialSlides = collect(old('slides', $slides))->map(function ($slide) {
            $src = $slide['src'] ?? '';
            $preview = $src
                ? (str_starts_with($src, 'http') ? $src : asset(ltrim($src, '/')))
                : '';

            return [
                'label' => $slide['label'] ?? '',
                'headline' => $slide['headline'] ?? '',
                'src' => $src,
                'category' => $slide['category'] ?? '',
                'preview' => $preview,
                'remove_image' => false,
            ];
        })->values()->all();
    @endphp

    <form
        class="space-y-6"
        method="POST"
        action="{{ route('admin.hero-carousel.update') }}"
        enctype="multipart/form-data"
        x-data="{
            slides: {{ \Illuminate\Support\Js::from($initialSlides) }},
            addSlide() {
                if (this.slides.length >= 6) return;
                this.slides.push({ label: '', headline: '', src: '', category: '', preview: '', remove_image: false });
            },
            removeSlide(index) {
                if (this.slides.length <= 1) return;
                this.slides.splice(index, 1);
            },
            setPreview(index, fileList) {
                const file = Array.from(fileList || []).find((item) => item.type.startsWith('image/'));
                if (! file) return;
                this.slides[index].preview = URL.createObjectURL(file);
                this.slides[index].remove_image = false;
            }
        }"
    >
        @csrf
        @method('PUT')

        <x-admin.form-header
            crumb="Dashboard / Administration / Hero carousel"
            title="Hero carousel"
            description="Manage the rotating images and captions on the homepage hero."
            :cancel="route('admin.dashboard')"
            submit="Save carousel"
        />

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-4">
                <template x-for="(slide, index) in slides" :key="index">
                    <section class="admin-card space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-lg">Slide <span x-text="index + 1"></span></h2>
                                <p class="mt-1 text-sm text-zinc-500">Image, label, and headline shown in the hero carousel.</p>
                            </div>
                            <button
                                type="button"
                                class="rounded-xl border border-zinc-200 px-3 py-1.5 text-xs font-semibold text-zinc-600 hover:border-[#d42127] hover:text-[#d42127]"
                                @click="removeSlide(index)"
                                x-show="slides.length > 1"
                            >
                                Remove
                            </button>
                        </div>

                        <input type="hidden" :name="`slides[${index}][src]`" :value="slide.src">
                        <input type="hidden" :name="`slides[${index}][category]`" :value="slide.category">

                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block text-sm font-semibold">Label
                                <input class="admin-input mt-2" :name="`slides[${index}][label]`" x-model="slide.label" required maxlength="80" placeholder="Graduation">
                            </label>
                            <label class="block text-sm font-semibold">Headline
                                <input class="admin-input mt-2" :name="`slides[${index}][headline]`" x-model="slide.headline" required maxlength="220" placeholder="Short caption for this slide">
                            </label>
                        </div>

                        <div class="flex flex-wrap items-start gap-4">
                            <div class="flex h-36 w-56 items-center justify-center overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50">
                                <img x-show="slide.preview" x-cloak :src="slide.preview" alt="" class="h-full w-full object-cover">
                                <span x-show="!slide.preview" class="px-3 text-center text-xs text-zinc-400">No custom image<br>Uses category / default</span>
                            </div>
                            <div class="min-w-[12rem] flex-1 space-y-3">
                                <label class="block text-sm font-medium text-zinc-700">
                                    Upload image
                                    <input
                                        class="admin-input mt-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#0f2744] file:px-3 file:py-1.5 file:text-white"
                                        type="file"
                                        :name="`slides[${index}][image]`"
                                        accept="image/png,image/jpeg,image/webp"
                                        @change="setPreview(index, $event.target.files)"
                                    >
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm font-medium text-zinc-700" x-show="slide.src || slide.preview">
                                    <input
                                        type="checkbox"
                                        :name="`slides[${index}][remove_image]`"
                                        value="1"
                                        @change="slide.remove_image = $event.target.checked; if ($event.target.checked) { slide.preview = ''; }"
                                    >
                                    Remove custom image
                                </label>
                                <p class="text-xs text-zinc-500">PNG, JPG or WEBP, up to 4MB. Recommended landscape crop.</p>
                            </div>
                        </div>
                    </section>
                </template>

                <button
                    type="button"
                    class="rounded-2xl border border-dashed border-zinc-300 px-4 py-3 text-sm font-semibold text-[#0f2744] hover:border-[#0f2744]"
                    @click="addSlide()"
                    x-show="slides.length < 6"
                >
                    + Add slide
                </button>
            </div>

            <aside class="admin-card space-y-4 lg:sticky lg:top-24 self-start">
                <h2>Save</h2>
                <p class="text-sm text-zinc-500">Changes appear immediately on the homepage hero carousel.</p>
                <p class="text-sm text-zinc-500">You can keep up to 6 slides. If no custom image is set, the matching category image or site default is used.</p>
                <x-admin.btn class="w-full" icon="save">Save carousel</x-admin.btn>
            </aside>
        </div>
    </form>
@endsection
