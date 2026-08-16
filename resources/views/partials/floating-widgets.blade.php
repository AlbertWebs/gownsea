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
            <input type="text" name="company" class="hidden" tabindex="-1" autocomplete="off">
            <input type="hidden" name="form_token" value="{{ \App\Support\InquiryFormGuard::token() }}">
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
    aria-label="Chat on WhatsApp"
    class="whatsapp-widget-trigger fixed bottom-5 right-4 z-40 md:right-6"
>
    <span class="whatsapp-widget-trigger__shell"></span>
    <span class="whatsapp-widget-trigger__core">
        <svg viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 text-white" aria-hidden="true">
            <path d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.02Zm-7.01 15.24h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.42 5.83c0 4.55-3.7 8.23-8.25 8.23Zm4.52-6.16c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.35-.76-1.84-.2-.48-.4-.42-.56-.42h-.48c-.17 0-.43.06-.66.31-.23.25-.87.85-.87 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.11-.23-.17-.48-.29Z"/>
        </svg>
    </span>
</a>
