<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="font-sans">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $meta['title'] ?? config('app.name') }}</title>
    <meta name="description" content="{{ $meta['description'] ?? 'Gownsea premium ceremonial attire.' }}">
    <meta name="theme-color" content="#d42127">
    <link rel="icon" href="{{ asset('favicon-rpimary.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('favicon-rpimary.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('favicon-rpimary.png') }}">
    <link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}">

    <meta property="og:site_name" content="Gownsea LTD">
    <meta property="og:title" content="{{ $meta['og_title'] ?? ($meta['title'] ?? config('app.name')) }}">
    <meta property="og:description" content="{{ $meta['og_description'] ?? ($meta['description'] ?? 'Gownsea premium ceremonial attire.') }}">
    <meta property="og:type" content="{{ $meta['og_type'] ?? 'website' }}">
    <meta property="og:url" content="{{ $meta['og_url'] ?? ($meta['canonical'] ?? url()->current()) }}">
    <meta property="og:image" content="{{ $meta['og_image'] ?? url('/favicon.ico') }}">

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

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body id="top" class="font-sans bg-white text-zinc-900 antialiased">
    @include('partials.header')

    @if (session('assistant_status'))
        <div class="container-shell mt-28">
            <p class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('assistant_status') }}
            </p>
        </div>
    @endif

    <main class="pt-28">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.floating-widgets')
</body>
</html>
