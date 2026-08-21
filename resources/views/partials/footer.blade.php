<footer class="site-footer">
    <div class="container-shell pt-10 pb-5 md:py-14">
        <div class="site-footer__cta surface-muted flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold text-[#0f2744]">Need graduation attire quickly?</p>
                <p class="mt-1 text-sm text-zinc-600">Talk to Gownsea for fast support, bulk planning, and delivery guidance.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('contact-us') }}" class="btn-primary">Contact Team</a>
                <a href="{{ route('bulk-inquiry') }}" class="btn-secondary">Start Bulk Inquiry</a>
            </div>
        </div>

        <div class="site-footer__columns">
            <div class="site-footer__brand">
                @php
                    $siteLogo = \App\Models\Setting::logoUrl();
                    $siteName = \App\Models\Setting::getValue('company_name', config('gownsea.brand.name'));
                @endphp
                @if ($siteLogo)
                    <a href="{{ route('home') }}" class="inline-flex">
                        <img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="site-footer__logo">
                    </a>
                @else
                    <p class="site-footer__title text-xl"><span class="text-[#d42127]">Gown</span><span class="text-[#0f2744]">sea LTD</span></p>
                @endif
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-zinc-600">
                    Premium graduation, legal, and church attire for hire and sale across Kenya with responsive support
                    for institutions and individual customers.
                </p>

                <div class="mt-5 space-y-2 text-sm text-zinc-700">
                    <p>{{ config('gownsea.brand.address') }}</p>
                    <p><a href="tel:{{ preg_replace('/\s+/', '', config('gownsea.brand.phone')) }}" class="transition-colors hover:text-[#0f2744]">{{ config('gownsea.brand.phone') }}</a></p>
                    <p><a href="mailto:{{ config('gownsea.brand.email') }}" class="transition-colors hover:text-[#0f2744]">{{ config('gownsea.brand.email') }}</a></p>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.5 8.5h2.3V5.4h-2.3c-2.6 0-4.3 1.6-4.3 4.3v1.7H8v3.2h2.2V20h3.2v-5.4h2.4l.5-3.2h-2.9V9.8c0-.8.4-1.3 1.1-1.3Z"/></svg>
                    </a>
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 7.4A4.6 4.6 0 1 0 16.6 12 4.6 4.6 0 0 0 12 7.4Zm0 7.6A3 3 0 1 1 15 12a3 3 0 0 1-3 3Zm5.8-8.7a1.1 1.1 0 1 0 1.1 1.1 1.1 1.1 0 0 0-1.1-1.1ZM12 4.5c2 0 2.3 0 3.1.1a4 4 0 0 1 2.6 1 4 4 0 0 1 1 2.6c.1.8.1 1.1.1 3.1s0 2.3-.1 3.1a4 4 0 0 1-1 2.6 4 4 0 0 1-2.6 1c-.8.1-1.1.1-3.1.1s-2.3 0-3.1-.1a4 4 0 0 1-2.6-1 4 4 0 0 1-1-2.6C4.5 14.3 4.5 14 4.5 12s0-2.3.1-3.1a4 4 0 0 1 1-2.6 4 4 0 0 1 2.6-1C9.7 4.5 10 4.5 12 4.5Zm0-1.5C9.9 3 9.6 3 8.8 3.1A5.5 5.5 0 0 0 5 4.9 5.5 5.5 0 0 0 3.1 8.8C3 9.6 3 9.9 3 12s0 2.4.1 3.2A5.5 5.5 0 0 0 5 19.1a5.5 5.5 0 0 0 3.8 1.8c.8.1 1.1.1 3.2.1s2.4 0 3.2-.1A5.5 5.5 0 0 0 19.1 19a5.5 5.5 0 0 0 1.8-3.8c.1-.8.1-1.1.1-3.2s0-2.4-.1-3.2A5.5 5.5 0 0 0 19.1 5 5.5 5.5 0 0 0 15.2 3.1C14.4 3 14.1 3 12 3Z"/></svg>
                    </a>
                    <a href="https://tiktok.com" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="TikTok">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.7 3v2.4a5.6 5.6 0 0 0 5.3 3.3v2.5a8 8 0 0 1-5.3-1.9v7.2A6.5 6.5 0 1 1 11 9.2v2.6a4 4 0 1 0 2.7 3.8V3Z"/></svg>
                    </a>
                    <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="LinkedIn">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.7 9.3H4V20h2.7V9.3ZM5.3 4A1.6 1.6 0 1 0 5.4 7.2 1.6 1.6 0 0 0 5.3 4ZM20 20h-2.7v-5.6c0-1.6-.6-2.6-2-2.6a2.1 2.1 0 0 0-2 1.5 2.7 2.7 0 0 0-.1 1v5.7H10.6s.1-9.2 0-10.2H13.3v1.6c.4-.8 1.6-2 3.8-2 2.7 0 4.9 1.8 4.9 5.6V20Z"/></svg>
                    </a>
                </div>
            </div>

            <div class="text-sm text-zinc-700">
                <p class="site-footer__title">Explore</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('home') }}" class="block transition-colors hover:text-[#0f2744]">Home</a>
                    <a href="{{ route('about-us') }}" class="block transition-colors hover:text-[#0f2744]">About Us</a>
                    <a href="{{ route('journal.index') }}" class="block transition-colors hover:text-[#0f2744]">The Gown Journal</a>
                    <a href="{{ route('bulk-inquiry') }}" class="block transition-colors hover:text-[#0f2744]">Bulk Hire</a>
                    <a href="{{ route('gown-for-hire') }}" class="block transition-colors hover:text-[#0f2744]">Hire a Gown</a>
                    <a href="{{ route('contact-us') }}" class="block transition-colors hover:text-[#0f2744]">Contact Us</a>
                    <a href="{{ route('sitemap') }}" class="block transition-colors hover:text-[#0f2744]">Sitemap.xml</a>
                </div>
            </div>

            <div class="text-sm text-zinc-700">
                <p class="site-footer__title">Attire Categories</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('graduation-attire') }}" class="block transition-colors hover:text-[#0f2744]">Graduation Attire</a>
                    <a href="{{ route('legal-attire') }}" class="block transition-colors hover:text-[#0f2744]">Legal Attire</a>
                    <a href="{{ route('church-wear') }}" class="block transition-colors hover:text-[#0f2744]">Church Wear</a>
                    <a href="{{ route('gown-for-hire') }}" class="block transition-colors hover:text-[#0f2744]">Gown for Hire</a>
                    <a href="/shop-attire-collection/graduation-attire/graduation-cap" class="block transition-colors hover:text-[#0f2744]">Graduation Cap</a>
                    <a href="/shop-attire-collection/graduation-attire/graduation-hoods" class="block transition-colors hover:text-[#0f2744]">Graduation Hoods</a>
                    <a href="/shop-attire-collection/graduation-attire/masters-gowns" class="block transition-colors hover:text-[#0f2744]">Masters Gowns</a>
                    <a href="/shop-attire-collection/graduation-attire/phd-gowns" class="block transition-colors hover:text-[#0f2744]">PhD Gowns</a>
                    <a href="/shop-attire-collection/graduation-attire/degree-gown" class="block transition-colors hover:text-[#0f2744]">Degree Gown</a>
                    <a href="/our-products/graduation-stoles" class="block transition-colors hover:text-[#0f2744]">Graduation Stoles</a>
                </div>
            </div>

            <div class="text-sm text-zinc-700">
                <p class="site-footer__title">Policies</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('privacy-policy') }}" class="block transition-colors hover:text-[#0f2744]">Privacy Policy</a>
                    <a href="{{ route('terms-and-conditions') }}" class="block transition-colors hover:text-[#0f2744]">Terms &amp; Conditions</a>
                    <a href="{{ route('return-policy') }}" class="block transition-colors hover:text-[#0f2744]">Return Policy</a>
                    <a href="{{ route('copyright') }}" class="block transition-colors hover:text-[#0f2744]">Copyright</a>
                </div>

                <div class="site-footer__promise">
                    <p class="site-footer__promise-kicker">
                        <span class="site-footer__promise-seal" aria-hidden="true">
                            <svg viewBox="0 0 16 16" fill="none">
                                <path d="M8 1.4 3.2 3.2v4.1c0 3.1 2.1 5.9 4.8 6.8 2.7-.9 4.8-3.7 4.8-6.8V3.2L8 1.4Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                                <path d="m5.6 7.6 1.6 1.6 3.2-3.2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Service Promise
                    </p>
                    <ul class="site-footer__promise-list">
                        <li>
                            <span class="site-footer__promise-icon" aria-hidden="true">
                                <svg viewBox="0 0 16 16" fill="none">
                                    <path d="M3.2 8.2V7.4A4.8 4.8 0 0 1 8 2.6a4.8 4.8 0 0 1 4.8 4.8v.8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                    <rect x="2.1" y="7.4" width="2.6" height="3.8" rx="1.1" stroke="currentColor" stroke-width="1.4"/>
                                    <rect x="11.3" y="7.4" width="2.6" height="3.8" rx="1.1" stroke="currentColor" stroke-width="1.4"/>
                                </svg>
                            </span>
                            <span>Responsive support for every order.</span>
                        </li>
                        <li>
                            <span class="site-footer__promise-icon" aria-hidden="true">
                                <svg viewBox="0 0 16 16" fill="none">
                                    <path d="M2.4 13.2V6.8L8 3.4l5.6 3.4v6.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M6.2 13.2V9.4h3.6v3.8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span>Bulk planning for institutions.</span>
                        </li>
                        <li>
                            <span class="site-footer__promise-icon" aria-hidden="true">
                                <svg viewBox="0 0 16 16" fill="none">
                                    <path d="m8 2.4 1.1 3.3h3.5L10.3 7.8l1.1 3.3L8 9.1l-3.4 2 1.1-3.3-2.3-2.1h3.5L8 2.4Z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span>Quality ceremonial finishing.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="site-footer__bottom">
            <p class="site-footer__copy">
                Copyright &copy; {{ now()->year }} Gownsea LTD. All rights reserved.
                <span class="site-footer__powered">Powered by <a href="http://designekta.com/" target="_blank" rel="noopener noreferrer">Designekta Studios</a></span>
            </p>
            <p class="site-footer__credit">Designed for a premium ceremonial shopping experience.</p>
            <a href="#top" class="site-footer__top hidden md:inline-flex" aria-label="Back to top" title="Back to top">
                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M8 12.5V3.5M8 3.5 4 7.5M8 3.5l4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </div>
</footer>
