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
    $phone = config('gownsea.brand.phone');
    $email = config('gownsea.brand.email');
    $address = config('gownsea.brand.address');
    $whatsapp = preg_replace('/\D+/', '', (string) config('gownsea.brand.whatsapp'));
    $phoneHref = 'tel:'.preg_replace('/\s+/', '', (string) $phone);
    $mapsHref = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode((string) $address);
@endphp

<header
    class="fixed inset-x-0 top-0 z-50 border-b border-zinc-200/80 bg-white/95 backdrop-blur"
    x-data="{ shopOpen: false, mobileOpen: false }"
>
    <div class="site-topbar">
        <div class="site-topbar__inner container-shell">
            <div class="site-topbar__group">
                <a href="{{ $mapsHref }}" target="_blank" rel="noopener noreferrer" class="min-w-0">
                    <span class="site-topbar__icon">
                        <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M8 1.6A5.2 5.2 0 0 0 2.8 6.8c0 3.7 4.1 7.1 5.2 7.9.3.2.7.2 1 0 1.1-.8 5.2-4.2 5.2-7.9A5.2 5.2 0 0 0 8 1.6Zm0 7.1A1.9 1.9 0 1 1 8 5a1.9 1.9 0 0 1 0 3.7Z"/></svg>
                    </span>
                    <span class="truncate">{{ $address }}</span>
                </a>
                <span class="hidden text-white/40 sm:inline" aria-hidden="true">|</span>
                <span class="hidden items-center gap-1.5 sm:inline-flex">
                    <span class="site-topbar__icon">
                        <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M8 1.4A6.6 6.6 0 1 0 14.6 8 6.61 6.61 0 0 0 8 1.4Zm.7 6.5-.2.1-2.3 1.5a.7.7 0 1 1-.8-1.1l1.9-1.3V4.6a.7.7 0 0 1 1.4 0v3.3Z"/></svg>
                    </span>
                    Mon–Sat, 8am–6pm
                </span>
            </div>

            <div class="site-topbar__group shrink-0">
                <a href="{{ $phoneHref }}">
                    <span class="site-topbar__icon">
                        <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M13.7 11.2 12 10.4a1.3 1.3 0 0 0-1.5.3l-.8.9A9.2 9.2 0 0 1 4.4 6.3l.9-.8a1.3 1.3 0 0 0 .3-1.5L4.8 2.3A1.3 1.3 0 0 0 3.4 1.6L1.8 2A1.3 1.3 0 0 0 1 3.3C1.6 10 6 14.4 12.7 15a1.3 1.3 0 0 0 1.3-.8l.4-1.6a1.3 1.3 0 0 0-.7-1.4Z"/></svg>
                    </span>
                    <span class="hidden sm:inline">{{ $phone }}</span>
                    <span class="sm:hidden">Call</span>
                </a>
                <a href="mailto:{{ $email }}" class="hidden md:inline-flex">
                    <span class="site-topbar__icon">
                        <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M1.5 4.2A1.7 1.7 0 0 1 3.2 2.6h9.6A1.7 1.7 0 0 1 14.5 4.2v.3L8 8.6 1.5 4.5v-.3Zm0 2.1 6.1 4a.8.8 0 0 0 .8 0l6.1-4V11.8a1.7 1.7 0 0 1-1.7 1.6H3.2A1.7 1.7 0 0 1 1.5 11.8V6.3Z"/></svg>
                    </span>
                    {{ $email }}
                </a>
                @if ($whatsapp)
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener noreferrer">
                        <span class="site-topbar__icon">
                            <svg viewBox="0 0 16 16" aria-hidden="true"><path d="M8 1.4A6.6 6.6 0 0 0 2.3 11.4L1.5 14.5l3.2-.8A6.6 6.6 0 1 0 8 1.4Zm3.4 9.3c-.14.4-.82.77-1.35.87-.36.07-.83.13-2.42-.52-2.03-.83-3.34-2.86-3.44-3-.1-.13-.84-1.12-.84-2.13 0-1.02.53-1.52.73-1.73.18-.2.4-.25.54-.25h.4c.13 0 .3 0 .46.35l.66 1.6c.05.12.09.23 0 .35l-.32.52c-.1.12-.22.27-.1.5.13.23.56.93 1.2 1.5.83.75 1.53 1 1.76 1.1.23.12.36.1.5-.06l.4-.47c.12-.14.27-.18.44-.12l1.58.74c.17.08.28.12.32.2.05.1.05.55-.18.95Z"/></svg>
                        </span>
                        WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="container-shell relative flex h-16 items-center justify-between">
        <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight text-zinc-950">
            <span class="text-[#d42127]">Gown</span>sea
        </a>

        <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
            <a href="{{ route('graduation-attire') }}" class="nav-link {{ $isGraduationNav ? 'is-active' : '' }}" @if ($isGraduationNav) aria-current="page" @endif>Graduation Attire</a>
            <a href="{{ route('legal-attire') }}" class="nav-link {{ $isLegalNav ? 'is-active' : '' }}" @if ($isLegalNav) aria-current="page" @endif>Legal Attire</a>
            <a href="{{ route('church-wear') }}" class="nav-link {{ $isChurchNav ? 'is-active' : '' }}" @if ($isChurchNav) aria-current="page" @endif>Church Wear</a>
            <a href="{{ route('about-us') }}" class="nav-link {{ $isAboutNav ? 'is-active' : '' }}" @if ($isAboutNav) aria-current="page" @endif>About Us</a>

            <div>
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
            </div>

            <a href="{{ route('journal.index') }}" class="nav-link {{ $isJournalNav ? 'is-active' : '' }}" @if ($isJournalNav) aria-current="page" @endif>The Gown Journal</a>
            <a href="{{ route('bulk-inquiry') }}" class="nav-link {{ $isBulkNav ? 'is-active' : '' }}" @if ($isBulkNav) aria-current="page" @endif>Bulk Hire</a>
        </nav>

        <a href="{{ route('contact-us') }}" class="btn-primary hidden md:inline-flex">Let's talk</a>

        <div
            x-show="shopOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            @click.away="shopOpen = false"
            @mouseenter="shopOpen = true"
            @mouseleave="shopOpen = false"
            class="absolute inset-x-0 top-full z-50 pt-3"
        >
            <div class="shop-menu mx-auto w-[48rem] max-w-full">
                <div class="shop-menu__grid">
                    <div class="shop-menu__col">
                        <a href="{{ route('graduation-attire') }}" class="shop-menu__heading">Graduation Attire</a>
                        <p class="shop-menu__intro">Caps, tassels, and academic hoods for hire and sale.</p>
                        <div class="shop-menu__links">
                            <a href="/shop-attire-collection/graduation-attire/graduation-cap">Graduation Cap</a>
                            <a href="/our-products/graduation-tassels">Graduation Tassels</a>
                            <a href="/our-products/undergraduate-academic-hoods">Undergraduate Hoods</a>
                            <a href="/shop-attire-collection/graduation-attire/graduation-hoods">Graduation Hoods</a>
                        </div>
                    </div>

                    <div class="shop-menu__col">
                        <a href="{{ route('legal-attire') }}" class="shop-menu__heading">Legal Attire</a>
                        <p class="shop-menu__intro">Courtroom-ready wigs, gowns, and advocate accessories.</p>
                        <div class="shop-menu__links">
                            <a href="/shop-attire/legal-attire">Bib</a>
                            <a href="/shop-attire/legal-attire">Advocates Shirt</a>
                            <a href="/shop-attire/legal-attire">Barrister Wig &amp; Gown</a>
                            <a href="{{ route('legal-attire') }}">Shop Legal Wear</a>
                        </div>
                    </div>

                    <div class="shop-menu__col">
                        <a href="{{ route('church-wear') }}" class="shop-menu__heading">Church Wear</a>
                        <p class="shop-menu__intro">Choir gowns, clergy robes, and liturgical garments.</p>
                        <div class="shop-menu__links">
                            <a href="/shop-attire/church-wear">Choir Gowns</a>
                            <a href="/shop-attire/church-wear">Clergy Robes</a>
                            <a href="/shop-attire/church-wear">Cassocks</a>
                            <a href="{{ route('church-wear') }}">Shop Church Wear</a>
                        </div>
                    </div>
                </div>

                <div class="shop-menu__footer">
                    <p>Hire &amp; sale · Nairobi delivery · Custom fit</p>
                    <div class="shop-menu__actions">
                        <a href="{{ route('graduation-attire') }}" class="shop-menu__text-link">Browse collections</a>
                        <a href="{{ route('contact-us') }}" class="btn-primary btn-sm">Get help</a>
                    </div>
                </div>
            </div>
        </div>

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
        <nav class="container-shell flex flex-col gap-2 py-4 text-sm font-medium">
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
