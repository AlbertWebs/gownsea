@extends('layouts.public')

@section('content')
    <article class="container-shell section-lg">
        <p class="kicker">{{ $post['category'] }}</p>
        <h1 class="mt-3 max-w-4xl font-semibold text-[#0f2744]">{{ $post['title'] }}</h1>
        <p class="mt-2 text-sm text-zinc-500">Published {{ \Illuminate\Support\Carbon::parse($post['date'])->format('F d, Y') }}</p>

        <div class="mt-8 surface-muted border-t-4 border-t-[#0f2744] p-6 md:p-8">
            <p class="leading-relaxed text-zinc-700">
                {{ $post['excerpt'] }} At Gownsea, every ceremony deserves attire that reflects pride, dignity, and confidence.
                Our team supports institutions and individuals with quality fitting, reliable timelines, and tailored recommendations.
            </p>
        </div>
    </article>

    <x-ui.cta-band
        title="Need help planning your gown requirements?"
        description="Our team can advise on packages, quantities, and timeline-friendly delivery options."
        primaryLabel="Contact Support"
        :primaryHref="route('contact-us')"
    />
@endsection
