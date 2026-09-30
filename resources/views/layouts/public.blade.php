<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="font-sans">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $meta['title'] ?? config('app.name') }}</title>
    <meta name="description" content="{{ $meta['description'] ?? 'Gownsea premium ceremonial attire.' }}">
    @if (filled($meta['robots'] ?? null))<meta name="robots" content="{{ $meta['robots'] }}">@endif
    <meta name="theme-color" content="#d42127">
    <link rel="icon" href="{{ asset('favicon-rpimary.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('favicon-rpimary.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('favicon-rpimary.png') }}">
    <link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}">

    <meta property="og:site_name" content="Gownsea LTD">
    <meta property="og:locale" content="en_KE">
    <meta property="og:title" content="{{ $meta['og_title'] ?? ($meta['title'] ?? config('app.name')) }}">
    <meta property="og:description" content="{{ $meta['og_description'] ?? ($meta['description'] ?? 'Gownsea premium ceremonial attire.') }}">
    <meta property="og:type" content="{{ $meta['og_type'] ?? 'website' }}">
    <meta property="og:url" content="{{ $meta['og_url'] ?? ($meta['canonical'] ?? url()->current()) }}">
    <meta property="og:image" content="{{ $meta['og_image'] ?? url('/favicon.ico') }}">
    @if (filled($meta['product_price'] ?? null) && filled($meta['product_currency'] ?? null))
        <meta property="product:price:amount" content="{{ $meta['product_price'] }}">
        <meta property="product:price:currency" content="{{ $meta['product_currency'] }}">
    @endif
    @if (filled($meta['product_availability'] ?? null))<meta property="product:availability" content="{{ $meta['product_availability'] }}">@endif
    @if (filled($meta['product_condition'] ?? null))<meta property="product:condition" content="{{ $meta['product_condition'] }}">@endif
    @if (filled($meta['product_sku'] ?? null))<meta property="product:retailer_item_id" content="{{ $meta['product_sku'] }}">@endif
    @if (filled($meta['product_brand'] ?? null))<meta property="product:brand" content="{{ $meta['product_brand'] }}">@endif

    <meta name="twitter:card" content="{{ $meta['twitter_card'] ?? 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $meta['twitter_title'] ?? ($meta['og_title'] ?? ($meta['title'] ?? config('app.name'))) }}">
    <meta name="twitter:description" content="{{ $meta['twitter_description'] ?? ($meta['og_description'] ?? ($meta['description'] ?? 'Gownsea premium ceremonial attire.')) }}">
    <meta name="twitter:image" content="{{ $meta['twitter_image'] ?? ($meta['og_image'] ?? url('/favicon.ico')) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400&family=Nunito:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="dns-prefetch" href="//images.unsplash.com">

    @stack('meta')
    @stack('json_ld')
    @php
        $siteUrl = url('/');
        $pageUrl = $meta['canonical'] ?? url()->current();
        $pageTitle = $meta['title'] ?? config('app.name');
        $pageDescription = $meta['description'] ?? 'Gownsea ceremonial attire in Kenya.';
        $brandSchema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => $siteUrl.'#organization',
                    'name' => 'Gownsea LTD',
                    'url' => $siteUrl,
                    'telephone' => config('gownsea.brand.phone'),
                    'email' => config('gownsea.brand.email'),
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => config('gownsea.brand.address'),
                        'addressLocality' => 'Nairobi',
                        'addressCountry' => 'KE',
                    ],
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $siteUrl.'#website',
                    'url' => $siteUrl,
                    'name' => 'Gownsea LTD',
                    'publisher' => ['@id' => $siteUrl.'#organization'],
                    'inLanguage' => 'en-KE',
                ],
                [
                    '@type' => 'WebPage',
                    '@id' => $pageUrl.'#webpage',
                    'url' => $pageUrl,
                    'name' => $pageTitle,
                    'description' => $pageDescription,
                    'isPartOf' => ['@id' => $siteUrl.'#website'],
                    'publisher' => ['@id' => $siteUrl.'#organization'],
                    'inLanguage' => 'en-KE',
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($brandSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body id="top" class="font-sans bg-white text-zinc-900 antialiased{{ \App\Models\Setting::mobileNavEnabled() ? ' has-mobile-dock' : '' }}">
    @include('partials.header')

    @if (session('assistant_status'))
        <div class="container-shell mt-4">
            <p class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('assistant_status') }}
            </p>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.floating-widgets')
    @if (\App\Models\Setting::mobileNavEnabled())
        @include('partials.mobile-dock')
    @endif
</body>
</html>
