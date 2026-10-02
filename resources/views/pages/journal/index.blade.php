@extends('layouts.public')

@push('json_ld')
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endpush

@section('content')
    <section class="container-shell section-lg">
        <x-ui.section-header
            kicker="The Gown Journal"
            title="Crafting confidence, one gown at a time"
            description="Insights and practical guidance on graduation attire, bulk planning, and ceremonial presentation."
        />

        <div class="luxury-grid mt-10 md:grid-cols-2">
            @foreach ($posts as $post)
                <article class="surface p-6">
                    @if(!empty($post['image']))<div class="relative mb-5"><img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" loading="lazy" class="aspect-[16/9] w-full rounded-xl object-cover">@if(count($post['images'] ?? []) > 1)<span class="absolute bottom-3 right-3 rounded-full bg-[#0f2744]/90 px-3 py-1 text-xs font-semibold text-white">{{ count($post['images']) }} photos</span>@endif</div>@endif
                    <p class="kicker">{{ $post['category'] }}</p>
                    <h3 class="mt-2 font-semibold">{{ $post['title'] }}</h3>
                    <p class="mt-3 text-sm text-zinc-600">{{ $post['excerpt'] }}</p>
                    <a class="mt-4 inline-block text-sm font-semibold underline" href="{{ route('journal.show', $post['slug']) }}">Read more</a>
                </article>
            @endforeach
        </div>
    </section>
@endsection
