<div
    x-data="{ open: false, message: '', faqs: @js(config('gownsea.assistant.faqs')) }"
    class="fixed bottom-5 left-4 z-50 md:left-6"
>
    <button
        type="button"
        aria-label="Open Gownsea assistant"
        class="assistant-widget-trigger group"
        @click="open = !open"
        :aria-expanded="open"
    >
        <span class="assistant-widget-trigger__shell"></span>
        <span class="assistant-widget-trigger__core">
            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-white" aria-hidden="true">
                <path d="M12 4.75c4.41 0 7.98 2.96 7.98 6.62 0 3.66-3.57 6.63-7.98 6.63-.79 0-1.56-.1-2.28-.3l-3.95 1.48 1.27-2.93C5.78 15.05 4.02 13.34 4.02 11.37c0-3.66 3.57-6.62 7.98-6.62Z" stroke="currentColor" stroke-width="1.55" stroke-linejoin="round"/>
                <circle cx="9.25" cy="11.37" r="0.95" fill="currentColor"/>
                <circle cx="12" cy="11.37" r="0.95" fill="currentColor"/>
                <circle cx="14.75" cy="11.37" r="0.95" fill="currentColor"/>
            </svg>
        </span>
        <span class="assistant-widget-trigger__dot" aria-hidden="true"></span>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        x-cloak
        class="mt-3 w-[min(92vw,360px)] rounded-2xl border border-zinc-200 bg-white p-4 shadow-2xl"
        @keydown.escape.window="open = false"
    >
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-zinc-900">Ask Gownsea Assistant</h3>
            <button type="button" class="text-xs text-zinc-500" @click="open = false">Close</button>
        </div>

        <div class="mb-4 grid gap-2">
            <template x-for="[label, faq] in Object.entries(faqs)" :key="label">
                <button
                    type="button"
                    class="rounded-lg border border-zinc-200 px-3 py-2 text-left text-xs text-zinc-700 transition-colors hover:border-[var(--brand-primary)] hover:text-[var(--brand-primary)]"
                    @click="message = faq"
                    x-text="label"
                ></button>
            </template>
        </div>

        <form method="POST" action="{{ route('assistant.submit') }}" class="space-y-2 text-sm">
            @csrf
            <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
            <input required name="name" type="text" class="w-full rounded-lg border border-zinc-300 px-3 py-2" placeholder="Name">
            <input required name="email" type="email" class="w-full rounded-lg border border-zinc-300 px-3 py-2" placeholder="Email">
            <input required name="phone" type="text" class="w-full rounded-lg border border-zinc-300 px-3 py-2" placeholder="Phone">
            <textarea required name="message" x-model="message" rows="3" class="w-full rounded-lg border border-zinc-300 px-3 py-2" placeholder="How can we help?"></textarea>
            <button type="submit" class="w-full rounded-lg bg-[var(--brand-primary)] px-4 py-2 font-semibold text-white transition-colors hover:brightness-90">Send message</button>
        </form>
    </div>
</div>

<a
    href="https://wa.me/{{ config('gownsea.brand.whatsapp') }}"
    target="_blank"
    rel="noopener noreferrer"
    class="fixed bottom-5 right-4 z-40 rounded-full bg-emerald-500 px-4 py-3 text-sm font-semibold text-white shadow-lg transition-transform duration-200 hover:-translate-y-0.5 md:right-6"
>
    WhatsApp
</a>
