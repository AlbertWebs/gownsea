<footer class="mt-20 border-t border-zinc-200 bg-zinc-50">
    <div class="container-shell py-14">
        <div class="surface-muted mb-10 flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold text-zinc-900">Need graduation attire quickly?</p>
                <p class="mt-1 text-sm text-zinc-600">Talk to Gownsea for fast support, bulk planning, and delivery guidance.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('contact-us') }}" class="btn-primary">Contact Team</a>
                <a href="{{ route('bulk-inquiry') }}" class="btn-secondary">Start Bulk Inquiry</a>
            </div>
        </div>

        <div class="grid gap-10 lg:grid-cols-[1.25fr_1fr_1fr_1fr]">
            <div>
                <p class="text-xl font-semibold"><span class="text-[#d42127]">Gown</span>sea LTD</p>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-zinc-600">
                    Premium graduation, legal, and church attire for hire and sale across Kenya with responsive support
                    for institutions and individual customers.
                </p>

                <div class="mt-5 space-y-2 text-sm text-zinc-700">
                    <p>{{ config('gownsea.brand.address') }}</p>
                    <p><a href="tel:{{ preg_replace('/\s+/', '', config('gownsea.brand.phone')) }}" class="transition-colors hover:text-[#d42127]">{{ config('gownsea.brand.phone') }}</a></p>
                    <p><a href="mailto:{{ config('gownsea.brand.email') }}" class="transition-colors hover:text-[#d42127]">{{ config('gownsea.brand.email') }}</a></p>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="rounded-full border border-zinc-300 px-3 py-1 text-xs font-semibold text-zinc-700 transition-colors hover:border-[#d42127] hover:text-[#d42127]">Facebook</a>
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="rounded-full border border-zinc-300 px-3 py-1 text-xs font-semibold text-zinc-700 transition-colors hover:border-[#d42127] hover:text-[#d42127]">Instagram</a>
                    <a href="https://tiktok.com" target="_blank" rel="noopener noreferrer" class="rounded-full border border-zinc-300 px-3 py-1 text-xs font-semibold text-zinc-700 transition-colors hover:border-[#d42127] hover:text-[#d42127]">TikTok</a>
                    <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="rounded-full border border-zinc-300 px-3 py-1 text-xs font-semibold text-zinc-700 transition-colors hover:border-[#d42127] hover:text-[#d42127]">LinkedIn</a>
                </div>
            </div>

            <div class="text-sm text-zinc-700">
                <p class="font-semibold text-zinc-900">Explore</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('home') }}" class="block transition-colors hover:text-[#d42127]">Home</a>
                    <a href="{{ route('about-us') }}" class="block transition-colors hover:text-[#d42127]">About Us</a>
                    <a href="{{ route('journal.index') }}" class="block transition-colors hover:text-[#d42127]">The Gown Journal</a>
                    <a href="{{ route('bulk-inquiry') }}" class="block transition-colors hover:text-[#d42127]">Bulk Hire</a>
                    <a href="{{ route('contact-us') }}" class="block transition-colors hover:text-[#d42127]">Contact Us</a>
                    <a href="{{ route('sitemap') }}" class="block transition-colors hover:text-[#d42127]">Sitemap.xml</a>
                </div>
            </div>

            <div class="text-sm text-zinc-700">
                <p class="font-semibold text-zinc-900">Attire Categories</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('shop-attire.collection', 'graduation-attire') }}" class="block transition-colors hover:text-[#d42127]">Graduation Attire</a>
                    <a href="{{ route('shop-attire.collection', 'legal-attire') }}" class="block transition-colors hover:text-[#d42127]">Legal Attire</a>
                    <a href="{{ route('shop-attire.collection', 'church-wear') }}" class="block transition-colors hover:text-[#d42127]">Church Wear</a>
                    <a href="/shop-attire-collection/graduation-attire/graduation-cap" class="block transition-colors hover:text-[#d42127]">Graduation Cap</a>
                    <a href="/shop-attire-collection/graduation-attire/graduation-hoods" class="block transition-colors hover:text-[#d42127]">Graduation Hoods</a>
                    <a href="/shop-attire-collection/graduation-attire/masters-gowns" class="block transition-colors hover:text-[#d42127]">Masters Gowns</a>
                    <a href="/shop-attire-collection/graduation-attire/phd-gowns" class="block transition-colors hover:text-[#d42127]">PhD Gowns</a>
                    <a href="/shop-attire-collection/graduation-attire/degree-gown" class="block transition-colors hover:text-[#d42127]">Degree Gown</a>
                    <a href="/our-products/graduation-stoles" class="block transition-colors hover:text-[#d42127]">Graduation Stoles</a>
                </div>
            </div>

            <div class="text-sm text-zinc-700">
                <p class="font-semibold text-zinc-900">Policies</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('privacy-policy') }}" class="block transition-colors hover:text-[#d42127]">Privacy Policy</a>
                    <a href="{{ route('terms-and-conditions') }}" class="block transition-colors hover:text-[#d42127]">Terms &amp; Conditions</a>
                    <a href="{{ route('return-policy') }}" class="block transition-colors hover:text-[#d42127]">Return Policy</a>
                    <a href="{{ route('copyright') }}" class="block transition-colors hover:text-[#d42127]">Copyright</a>
                </div>

                <div class="surface mt-6 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#d42127]">Service Promise</p>
                    <ul class="mt-3 space-y-2 text-xs text-zinc-600">
                        <li>Responsive support for every order.</li>
                        <li>Bulk planning for institutions.</li>
                        <li>Quality ceremonial finishing.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-10 border-t border-zinc-200 pt-5">
            <div class="flex flex-col gap-2 text-xs text-zinc-500 md:flex-row md:items-center md:justify-between">
                <p>Copyright &copy; {{ now()->year }} Gownsea LTD. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <p>Designed for a premium ceremonial shopping experience.</p>
                    <a href="#top" class="font-semibold text-zinc-700 transition-colors hover:text-[#d42127]">Back to top</a>
                </div>
            </div>
        </div>
    </div>
</footer>
