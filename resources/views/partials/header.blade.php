@php
    $isGraduationNav = request()->routeIs('graduation-attire')
        || request()->routeIs('gown-for-hire')
        || request()->is('shop-attire/graduation-attire*')
        || request()->is('shop-attire-collection/graduation-attire*')
        || request()->is('our-products/*');
    $isLegalNav = request()->routeIs('legal-attire')
        || request()->is('shop-attire/legal-attire*')
        || request()->is('shop-attire-collection/legal-attire*');
    $isChurchNav = request()->routeIs('church-wear')
        || request()->is('shop-attire/church-wear*')
        || request()->is('shop-attire-collection/church-wear*');
    $isShopNav = ! $isGraduationNav && ! $isLegalNav && ! $isChurchNav && (
        request()->is('shop-attire*')
        || request()->is('shop-attire-collection*')
        || request()->is('our-products*')
    );
    $isAboutNav = request()->routeIs('about-us');
    $isJournalNav = request()->routeIs('journal.index', 'journal.show');
    $isBulkNav = request()->routeIs('bulk-inquiry');
@endphp

<header
    class="fixed inset-x-0 top-0 z-50 border-b border-zinc-200/80 bg-white/95 backdrop-blur"
    x-data="{ shopOpen: false, mobileOpen: false }"
>
    <div class="flex h-16 w-full items-center justify-between px-4 md:px-8 xl:px-10">
        <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight text-zinc-950">
            <span class="text-[#d42127]">Gown</span>sea
        </a>

        <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
            <a href="{{ route('graduation-attire') }}" class="nav-link {{ $isGraduationNav ? 'is-active' : '' }}" @if ($isGraduationNav) aria-current="page" @endif>Graduation Attire</a>
            <a href="{{ route('legal-attire') }}" class="nav-link {{ $isLegalNav ? 'is-active' : '' }}" @if ($isLegalNav) aria-current="page" @endif>Legal Attire</a>
            <a href="{{ route('church-wear') }}" class="nav-link {{ $isChurchNav ? 'is-active' : '' }}" @if ($isChurchNav) aria-current="page" @endif>Church Wear</a>
            <a href="{{ route('about-us') }}" class="nav-link {{ $isAboutNav ? 'is-active' : '' }}" @if ($isAboutNav) aria-current="page" @endif>About Us</a>

            <div class="relative">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-md py-2 transition-colors hover:text-[#d42127] {{ $isShopNav ? 'text-[#d42127]' : '' }}"
                    @click="shopOpen = !shopOpen"
                    @mouseenter="shopOpen = true"
                    aria-haspopup="true"
                    :aria-expanded="shopOpen"
                    @if ($isShopNav) aria-current="page" @endif
                >
                    Shop Attire
                    <svg class="h-4 w-4 text-zinc-500 transition-transform duration-200" :class="{ 'rotate-180': shopOpen }" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <div
                    x-show="shopOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    x-cloak
                    @click.away="shopOpen = false"
                    @mouseleave="shopOpen = false"
                    class="absolute left-0 top-full z-50 mt-3 w-[420px] rounded-2xl border border-zinc-200 bg-white p-5 shadow-xl"
                >
                    <div class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-[#d42127]">Graduation Attire</div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="/shop-attire-collection/graduation-attire/graduation-cap" class="rounded-lg border border-zinc-200 px-3 py-2 text-xs text-zinc-700 transition-colors hover:border-[#d42127] hover:text-[#d42127]">Graduation Cap</a>
                        <a href="/our-products/graduation-tassels" class="rounded-lg border border-zinc-200 px-3 py-2 text-xs text-zinc-700 transition-colors hover:border-[#d42127] hover:text-[#d42127]">Graduation Tassels</a>
                        <a href="/our-products/undergraduate-academic-hoods" class="rounded-lg border border-zinc-200 px-3 py-2 text-xs text-zinc-700 transition-colors hover:border-[#d42127] hover:text-[#d42127]">Undergraduate Hoods</a>
                        <a href="/shop-attire-collection/graduation-attire/graduation-hoods" class="rounded-lg border border-zinc-200 px-3 py-2 text-xs text-zinc-700 transition-colors hover:border-[#d42127] hover:text-[#d42127]">Graduation Hoods</a>
                    </div>

                    <div class="mb-2 mt-4 text-xs font-semibold uppercase tracking-[0.2em] text-[#d42127]">Legal Attire</div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="/shop-attire/legal-attire" class="rounded-lg border border-zinc-200 px-3 py-2 text-xs text-zinc-700 transition-colors hover:border-[#d42127] hover:text-[#d42127]">Bib</a>
                        <a href="/shop-attire/legal-attire" class="rounded-lg border border-zinc-200 px-3 py-2 text-xs text-zinc-700 transition-colors hover:border-[#d42127] hover:text-[#d42127]">Advocates Shirt</a>
                    </div>

                    <div class="mt-4 flex items-center justify-between border-t border-zinc-100 pt-4">
                        <a href="{{ route('graduation-attire') }}" class="text-xs font-semibold text-zinc-900 transition-colors hover:text-[#d42127]">Browse collections</a>
                        <a href="{{ route('contact-us') }}" class="rounded-full bg-[#d42127] px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-[#b51a22]">Get help</a>
                    </div>
                </div>
            </div>

            <a href="{{ route('journal.index') }}" class="nav-link {{ $isJournalNav ? 'is-active' : '' }}" @if ($isJournalNav) aria-current="page" @endif>The Gown Journal</a>
            <a href="{{ route('bulk-inquiry') }}" class="nav-link {{ $isBulkNav ? 'is-active' : '' }}" @if ($isBulkNav) aria-current="page" @endif>Bulk Hire</a>
        </nav>

        <a href="{{ route('contact-us') }}" class="btn-primary hidden md:inline-flex">Let's talk</a>

        <button
            type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-zinc-300 text-zinc-700 transition-colors hover:border-zinc-400 md:hidden"
            @click="mobileOpen = !mobileOpen"
            :aria-expanded="mobileOpen"
            aria-controls="mobile-menu"
            aria-label="Toggle navigation menu"
        >
            <span class="relative block h-4 w-5">
                <span
                    class="absolute left-0 top-0 h-0.5 w-5 rounded bg-current transition-all duration-300"
                    :class="mobileOpen ? 'top-1.5 rotate-45' : ''"
                ></span>
                <span
                    class="absolute left-0 top-1.5 h-0.5 w-5 rounded bg-current transition-all duration-300"
                    :class="mobileOpen ? 'opacity-0' : 'opacity-100'"
                ></span>
                <span
                    class="absolute left-0 top-3 h-0.5 w-5 rounded bg-current transition-all duration-300"
                    :class="mobileOpen ? 'top-1.5 -rotate-45' : ''"
                ></span>
            </span>
        </button>
    </div>

    <div
        id="mobile-menu"
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        x-cloak
        class="border-t border-zinc-200 bg-white/95 backdrop-blur md:hidden"
    >
        <nav class="flex w-full flex-col gap-2 px-4 py-4 text-sm font-medium md:px-8">
            <a href="{{ route('graduation-attire') }}" class="nav-link-mobile {{ $isGraduationNav ? 'is-active' : '' }}" @if ($isGraduationNav) aria-current="page" @endif>Graduation Attire</a>
            <a href="{{ route('legal-attire') }}" class="nav-link-mobile {{ $isLegalNav ? 'is-active' : '' }}" @if ($isLegalNav) aria-current="page" @endif>Legal Attire</a>
            <a href="{{ route('church-wear') }}" class="nav-link-mobile {{ $isChurchNav ? 'is-active' : '' }}" @if ($isChurchNav) aria-current="page" @endif>Church Wear</a>
            <a href="{{ route('about-us') }}" class="nav-link-mobile {{ $isAboutNav ? 'is-active' : '' }}" @if ($isAboutNav) aria-current="page" @endif>About Us</a>
            <a href="{{ route('journal.index') }}" class="nav-link-mobile {{ $isJournalNav ? 'is-active' : '' }}" @if ($isJournalNav) aria-current="page" @endif>The Gown Journal</a>
            <a href="{{ route('bulk-inquiry') }}" class="nav-link-mobile {{ $isBulkNav ? 'is-active' : '' }}" @if ($isBulkNav) aria-current="page" @endif>Bulk Hire</a>
            <a href="{{ route('contact-us') }}" class="btn-primary mt-2">Let's talk</a>
        </nav>
    </div>
</header>
