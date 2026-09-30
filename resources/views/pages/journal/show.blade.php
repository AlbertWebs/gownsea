@extends('layouts.public')

@push('meta')
    <meta name="robots" content="index,follow,max-image-preview:large">
    <meta name="author" content="{{ $articleAuthor['name'] }}">
    <meta property="article:published_time" content="{{ $publishedAt->toAtomString() }}">
    <meta property="article:modified_time" content="{{ $modifiedAt->toAtomString() }}">
    <meta property="article:section" content="{{ $post['category'] }}">
    @foreach($articleKeywords as $keyword)
        <meta property="article:tag" content="{{ $keyword }}">
    @endforeach
@endpush

@push('json_ld')
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endpush

@section('content')
    <article class="journal-article container-medium section-lg">
        <nav class="mb-8 text-sm text-zinc-500" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a class="transition-colors hover:text-[#d42127]" href="{{ route('home') }}">Home</a></li>
                <li aria-hidden="true">/</li>
                <li><a class="transition-colors hover:text-[#d42127]" href="{{ route('journal.index') }}">The Gown Journal</a></li>
                <li aria-hidden="true">/</li>
                <li class="font-medium text-zinc-800" aria-current="page">{{ $post['display_title'] }}</li>
            </ol>
        </nav>
        <header class="journal-article__header mx-auto max-w-4xl text-center">
            <p class="kicker mt-7">{{ $post['category'] }}</p>
            <h1 class="journal-article__title mt-4 text-[#0f2744]">{{ $post['display_title'] }}</h1>
            <p class="journal-article__date mt-5">Published <time datetime="{{ $publishedAt->toDateString() }}">{{ $publishedAt->format('F d, Y') }}</time></p>
            @if(filled($post['body'] ?? null))
                <p class="journal-article__standfirst">{{ $post['excerpt'] }}</p>
            @endif
        </header>

        @if(!empty($post['image']))
            <img src="{{ $post['image'] }}" alt="{{ $post['display_title'] }}" class="journal-article__cover">
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
