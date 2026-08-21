@php
    $dockGraduation = request()->routeIs('graduation-attire')
        || request()->routeIs('gown-for-hire')
        || request()->is('shop-attire/graduation-attire*')
        || request()->is('shop-attire-collection/graduation-attire*')
        || request()->is('our-products/*');
    $dockLegal = request()->routeIs('legal-attire')
        || request()->is('shop-attire/legal-attire*')
        || request()->is('shop-attire-collection/legal-attire*');
    $dockClergy = request()->routeIs('church-wear')
        || request()->is('shop-attire/church-wear*')
        || request()->is('shop-attire-collection/church-wear*');
    $dockBook = request()->routeIs('contact-us', 'bulk-inquiry');
@endphp

<nav class="mobile-dock md:hidden" aria-label="Quick mobile links">
    <a
        href="{{ route('graduation-attire') }}"
        class="mobile-dock__btn {{ $dockGraduation ? 'is-active' : '' }}"
        @if ($dockGraduation) aria-current="page" @endif
        aria-label="Graduation attire"
    >
        <span class="mobile-dock__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M3 10.2 12 6l9 4.2-9 4.2L3 10.2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M7.2 12.2v4.1c0 .3 2.1 2.2 4.8 2.2s4.8-1.9 4.8-2.2v-4.1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="M21 10.5v5.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
        </span>
        <span class="mobile-dock__label">Graduation</span>
    </a>

    <a
        href="{{ route('legal-attire') }}"
        class="mobile-dock__btn {{ $dockLegal ? 'is-active' : '' }}"
        @if ($dockLegal) aria-current="page" @endif
        aria-label="Legal attire"
    >
        <span class="mobile-dock__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M12 4.6v14.8M8 19.4h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="M12 7.2 6.2 13h4.2L12 7.2Zm0 0 5.8 5.8h-4.2L12 7.2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M6.2 13c0 1.5-.8 2.6-1.8 3.6M17.8 13c0 1.5.8 2.6 1.8 3.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
        </span>
        <span class="mobile-dock__label">Legal</span>
    </a>

    <a
        href="{{ route('church-wear') }}"
        class="mobile-dock__btn {{ $dockClergy ? 'is-active' : '' }}"
        @if ($dockClergy) aria-current="page" @endif
        aria-label="Clergy regalia"
    >
        <span class="mobile-dock__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M12 3.6v5.2M9.8 6.2h4.4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="M6.4 20.2V12l5.6-4.4 5.6 4.4v8.2" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M10.4 20.2v-4.2h3.2v4.2" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            </svg>
        </span>
        <span class="mobile-dock__label">Clergy</span>
    </a>

    <a
        href="{{ route('contact-us') }}"
        class="mobile-dock__btn mobile-dock__btn--book {{ $dockBook ? 'is-active' : '' }}"
        @if ($dockBook) aria-current="page" @endif
        aria-label="Book now"
    >
        <span class="mobile-dock__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M7.2 4.6v2.2M16.8 4.6v2.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <rect x="4.6" y="6.2" width="14.8" height="13.2" rx="2" stroke="currentColor" stroke-width="1.7"/>
                <path d="M4.6 10.2h14.8" stroke="currentColor" stroke-width="1.7"/>
                <path d="m9.2 14.6 1.8 1.8 3.8-3.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <span class="mobile-dock__label">Book now</span>
    </a>
</nav>
