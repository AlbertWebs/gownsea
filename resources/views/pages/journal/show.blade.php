@extends('layouts.public')

@section('content')
    <article class="container-shell section-lg">
        <p class="kicker">{{ $post['category'] }}</p>
        <h1 class="mt-3 max-w-4xl font-semibold text-[#0f2744]">{{ $post['title'] }}</h1>
        <p class="mt-2 text-sm text-zinc-500">Published {{ \Illuminate\Support\Carbon::parse($post['date'])->format('F d, Y') }}</p>

        @if(!empty($post['image']))<img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" class="mt-8 max-h-[32rem] w-full rounded-2xl object-cover">@endif
        <div class="prose mt-8 max-w-none leading-relaxed text-zinc-700">
            @if(filled($post['body'] ?? null))
                {!! $post['body'] !!}
            @else
                <p>{{ $post['excerpt'] }}</p>
            @endif
        </div>
    </article>

    <x-ui.cta-band
        title="Need help planning your gown requirements?"
        description="Our team can advise on packages, quantities, and timeline-friendly delivery options."
        primaryLabel="Contact Support"
        :primaryHref="route('contact-us')"
    />
@endsection
