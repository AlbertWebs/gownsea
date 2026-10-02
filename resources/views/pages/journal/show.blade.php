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

        @if(count($post['images'] ?? []) > 1)
            <section class="journal-gallery" aria-label="Article photo gallery">
                <h2 class="journal-gallery__title">More photos</h2>
                <div class="journal-gallery__grid">
                    @foreach(array_slice($post['images'], 1) as $index => $galleryImage)
                        <button type="button" class="journal-gallery__item" data-gallery-index="{{ $index }}" aria-label="View article photo {{ $index + 2 }}">
                            <img src="{{ $galleryImage }}" alt="{{ $post['display_title'] }} — photo {{ $index + 2 }}" loading="lazy">
                        </button>
                    @endforeach
                </div>
            </section>
            <dialog class="journal-lightbox" id="journal-lightbox" aria-label="Photo viewer">
                <div class="journal-lightbox__topbar">
                    <span id="journal-lightbox-count" aria-live="polite"></span>
                    <button type="button" class="journal-lightbox__close" aria-label="Close photo viewer">Close <span aria-hidden="true">×</span></button>
                </div>
                <div class="journal-lightbox__stage">
                    <button type="button" class="journal-lightbox__nav" data-gallery-previous aria-label="Previous photo">‹</button>
                    <img id="journal-lightbox-image" src="" alt="">
                    <button type="button" class="journal-lightbox__nav" data-gallery-next aria-label="Next photo">›</button>
                </div>
                <p class="journal-lightbox__caption" id="journal-lightbox-caption"></p>
            </dialog>
            <script>
                (() => {
                    const dialog = document.getElementById('journal-lightbox');
                    const items = [...document.querySelectorAll('[data-gallery-index]')];
                    if (!dialog || !items.length) return;
                    const image = document.getElementById('journal-lightbox-image');
                    const caption = document.getElementById('journal-lightbox-caption');
                    const count = document.getElementById('journal-lightbox-count');
                    let activeIndex = 0;
                    const showImage = (index) => {
                        activeIndex = (index + items.length) % items.length;
                        const thumbnail = items[activeIndex].querySelector('img');
                        image.src = thumbnail.currentSrc || thumbnail.src;
                        image.alt = thumbnail.alt;
                        caption.textContent = thumbnail.alt;
                        count.textContent = `${activeIndex + 1} / ${items.length}`;
                    };
                    items.forEach((item, index) => item.addEventListener('click', () => {
                        showImage(index);
                        dialog.showModal();
                        dialog.querySelector('.journal-lightbox__close').focus();
                    }));
                    dialog.querySelector('.journal-lightbox__close').addEventListener('click', () => dialog.close());
                    dialog.querySelector('[data-gallery-previous]').addEventListener('click', () => showImage(activeIndex - 1));
                    dialog.querySelector('[data-gallery-next]').addEventListener('click', () => showImage(activeIndex + 1));
                    dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
                    dialog.addEventListener('keydown', (event) => {
                        if (event.key === 'ArrowLeft') showImage(activeIndex - 1);
                        if (event.key === 'ArrowRight') showImage(activeIndex + 1);
                    });
                })();
            </script>
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
