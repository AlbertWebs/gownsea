@extends('layouts.public')

@section('content')
    <article class="journal-article container-medium section-lg">
        <header class="journal-article__header mx-auto max-w-4xl text-center">
            <a class="journal-article__back" href="{{ route('journal.index') }}">← The Gown Journal</a>
            <p class="kicker mt-7">{{ $post['category'] }}</p>
            <h1 class="journal-article__title mt-4 text-[#0f2744]">{{ $post['title'] }}</h1>
            <p class="journal-article__date mt-5">Published {{ \Illuminate\Support\Carbon::parse($post['date'])->format('F d, Y') }}</p>
            @if(filled($post['body'] ?? null))
                <p class="journal-article__standfirst">{{ $post['excerpt'] }}</p>
            @endif
        </header>

        @if(!empty($post['image']))
            <img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" class="journal-article__cover">
        @endif

        <div class="journal-article__body">
            @if(filled($post['body'] ?? null))
                {!! $post['body'] !!}
            @else
                <p>{{ $post['excerpt'] }}</p>
            @endif
        </div>

        <footer class="journal-article__footer">
            <a href="{{ route('journal.index') }}">Back to all articles <span aria-hidden="true">→</span></a>
        </footer>
    </article>

    <x-ui.cta-band
        title="Need help planning your gown requirements?"
        description="Our team can advise on packages, quantities, and timeline-friendly delivery options."
        primaryLabel="Contact Support"
        :primaryHref="route('contact-us')"
    />
@endsection
