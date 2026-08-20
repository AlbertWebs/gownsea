@extends('layouts.admin')
@section('title', 'Website settings')
@section('content')
    <form class="space-y-6" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" x-data="{ tab: 'company' }">
        @csrf @method('PUT')

        <x-admin.form-header
            crumb="Dashboard / Administration / Settings"
            title="Website settings"
            description="These details appear on the public site and in quotes."
            :cancel="route('admin.dashboard')"
            submit="Save settings"
        />

        <div class="flex flex-wrap gap-2 border-b border-zinc-200 pb-2 text-sm">
            <button type="button" class="rounded-2xl px-4 py-2" :class="tab === 'company' ? 'bg-[#0f2744] text-white' : 'bg-zinc-100 text-zinc-700'" @click="tab = 'company'">Company</button>
            <button type="button" class="rounded-2xl px-4 py-2" :class="tab === 'seo' ? 'bg-[#0f2744] text-white' : 'bg-zinc-100 text-zinc-700'" @click="tab = 'seo'">SEO</button>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-6">
                <section class="admin-card space-y-4" x-show="tab === 'company'">
                    <div>
                        <h2>Company</h2>
                        <p class="mt-1 text-sm text-zinc-500">Contact details used in the header, footer and enquiry messages.</p>
                    </div>
                    @foreach (['company_name' => 'Company name', 'phone' => 'Phone', 'email' => 'Email', 'whatsapp' => 'WhatsApp number', 'address' => 'Address', 'currency' => 'Currency'] as $key => $label)
                        <label class="block text-sm font-semibold">{{ $label }}
                            <input class="admin-input mt-2" name="{{ $key }}" value="{{ $settings[$key] ?? '' }}">
                        </label>
                    @endforeach

                    <div
                        class="space-y-3"
                        x-data="{ preview: {{ \Illuminate\Support\Js::from($settings['logo'] ? asset(ltrim($settings['logo'], '/')) : '') }} }"
                    >
                        <p class="text-sm font-semibold">Website logo</p>
                        <p class="text-sm text-zinc-500">Shown in the public header and footer. PNG, JPG or WEBP, up to 2MB.</p>
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex h-20 w-40 items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 p-2">
                                <img x-show="preview" x-cloak :src="preview" alt="Logo preview" class="max-h-16 max-w-full object-contain">
                                <span x-show="!preview" class="text-xs text-zinc-400">No logo yet</span>
                            </div>
                            <label class="block text-sm font-medium text-zinc-700">
                                Choose file
                                <input
                                    class="admin-input mt-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#0f2744] file:px-3 file:py-1.5 file:text-white"
                                    type="file"
                                    name="logo"
                                    accept="image/png,image/jpeg,image/webp"
                                    @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview"
                                >
                            </label>
                        </div>
                        @error('logo')<span class="block text-xs text-[#d42127]">{{ $message }}</span>@enderror
                        @if (! empty($settings['logo']))
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-zinc-700">
                                <input type="checkbox" name="remove_logo" value="1">
                                Remove current logo
                            </label>
                        @endif
                    </div>
                </section>
                <section class="admin-card space-y-4" x-show="tab === 'seo'">
                    <div>
                        <h2>Default SEO</h2>
                        <p class="mt-1 text-sm text-zinc-500">Fallbacks when a page does not set its own meta tags.</p>
                    </div>
                    <label class="block text-sm font-semibold">Default SEO title
                        <input class="admin-input mt-2" name="seo_title" value="{{ $settings['seo_title'] ?? '' }}">
                    </label>
                    <label class="block text-sm font-semibold">Default meta description
                        <textarea class="admin-input mt-2" name="seo_description" rows="4">{{ $settings['seo_description'] ?? '' }}</textarea>
                    </label>
                </section>
            </div>
            <aside class="admin-card space-y-4 lg:sticky lg:top-24 self-start">
                <h2>Save</h2>
                <p class="text-sm text-zinc-500">Changes apply immediately on the public website.</p>
                <x-admin.btn class="w-full" icon="save">Save settings</x-admin.btn>
            </aside>
        </div>
    </form>
@endsection
