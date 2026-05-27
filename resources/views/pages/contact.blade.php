@extends('layouts.public')

@section('content')
    <section class="container-shell section-lg">
        <x-ui.section-header
            kicker="Let's talk"
            title="Contact Us"
            description="Speak with our team for orders, pricing, and delivery timelines."
        />

        <div class="luxury-grid mt-8 md:grid-cols-2">
            <div class="surface p-6 text-sm text-zinc-700">
                <p class="font-semibold text-zinc-900">Office</p>
                <p class="mt-3">{{ config('gownsea.brand.address') }}</p>
                <p class="mt-2">{{ config('gownsea.brand.phone') }}</p>
                <p class="mt-2">{{ config('gownsea.brand.email') }}</p>
                <a href="{{ route('bulk-inquiry') }}" class="mt-3 inline-block font-semibold text-[#d42127] underline">Need bulk hire? Send inquiry</a>
            </div>

            <div class="surface p-6">
                <h3 class="font-semibold">Common Questions</h3>
                <ul class="mt-4 space-y-3 text-sm text-zinc-600">
                    @foreach ($faqs as $label => $faq)
                        <li><span class="font-semibold text-zinc-900">{{ $label }}:</span> {{ $faq }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
@endsection
