<!DOCTYPE html>
<html lang="en" class="font-sans">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Login') | Gownsea</title>
    <link rel="icon" href="{{ asset('favicon-rpimary.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('favicon-rpimary.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('favicon-rpimary.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="admin-login font-sans min-h-screen antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="admin-login__card w-full max-w-[440px]">
            <header class="admin-login__header">
                <div>
                    <p class="admin-login__kicker">Admin access</p>
                    <h1 class="admin-login__title">@yield('heading', 'Gownsea')</h1>
                    <p class="admin-login__lede">@yield('lede', 'Sign in to manage content and settings.')</p>
                </div>
                <span class="admin-login__avatar" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8.2" r="3.1" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M5.8 18.4c.9-3.1 3.2-4.6 6.2-4.6s5.3 1.5 6.2 4.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </span>
            </header>

            @if (session('status'))
                <p class="mt-5 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('status') }}</p>
            @endif
            @if ($errors->any())
                <p class="mt-5 rounded-lg bg-red-50 px-3 py-2 text-sm text-[#d42127]">{{ $errors->first() }}</p>
            @endif

            @yield('content')
        </section>
    </main>
</body>
</html>
