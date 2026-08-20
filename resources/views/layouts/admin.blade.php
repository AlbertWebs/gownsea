<!DOCTYPE html>
<html lang="en" class="font-sans">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | Gownsea</title>
    <link rel="icon" href="{{ asset('favicon-rpimary.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('favicon-rpimary.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('favicon-rpimary.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:wght@600;700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
</head>
<body class="admin-app font-sans text-zinc-900 antialiased" x-data="{ sidebar: false }">
    <div class="flex min-h-screen">
        <aside class="admin-sidebar hidden lg:sticky lg:top-0 lg:block lg:h-screen lg:overflow-hidden">
            @include('admin.partials.sidebar')
        </aside>
        <div
            x-show="sidebar"
            x-cloak
            class="fixed inset-0 z-40 lg:hidden"
        >
            <div class="absolute inset-0 bg-zinc-950/40" @click="sidebar = false"></div>
            <aside class="admin-sidebar relative z-10 h-full overflow-hidden">
                @include('admin.partials.sidebar')
            </aside>
        </div>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-20 flex items-center gap-3 border-b border-zinc-200 bg-white px-4 py-3">
                <button type="button" class="rounded-lg border border-zinc-200 px-3 py-2 text-sm lg:hidden" @click="sidebar = true">Menu</button>
                <form action="{{ route('admin.search') }}" class="min-w-0 flex-1">
                    <input name="q" value="{{ request('q') }}" class="admin-input max-w-xl" placeholder="Search products, customers, leads, inquiries, sales...">
                </form>
                <div class="relative" x-data="{ open: false }">
                    <button type="button" class="rounded-2xl border border-zinc-200 px-3 py-2 text-sm" @click="open = !open">
                        Alerts @if(($unreadNotifications ?? 0) > 0)<span class="ml-1 text-[#d42127]">{{ $unreadNotifications }}</span>@endif
                    </button>
                    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-80 rounded-xl border border-zinc-200 bg-white p-3 shadow-lg">
                        <form method="POST" action="{{ route('admin.notifications.read-all') }}">@csrf
                            <button class="text-xs text-[#d42127]">Mark all as read</button>
                        </form>
                        <div class="mt-2 space-y-2">
                            @forelse ($adminNotifications ?? [] as $note)
                                <a href="{{ route('admin.notifications.read', $note) }}" class="block rounded-lg px-2 py-2 text-sm hover:bg-zinc-50 {{ $note->read_at ? 'text-zinc-500' : 'text-zinc-900' }}">
                                    <strong>{{ $note->title }}</strong>
                                    <span class="block text-xs">{{ $note->body }}</span>
                                </a>
                            @empty
                                <p class="text-sm text-zinc-500">No notifications yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-4 md:p-6">
                @if (session('status'))
                    <p class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</p>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ $errors->first() }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
