@extends('layouts.admin')
@section('title', 'Website settings')
@section('content')
    <form class="space-y-6" method="POST" action="{{ route('admin.settings.update') }}" x-data="{ tab: 'company' }">
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
