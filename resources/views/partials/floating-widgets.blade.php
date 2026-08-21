@php
    $whatsapp = preg_replace('/\D+/', '', (string) config('gownsea.brand.whatsapp'));
@endphp

<div
    x-data="{
        open: false,
        message: '',
        topic: '',
        faqs: @js(config('gownsea.assistant.faqs')),
        pick(label, faq) {
            this.topic = label;
            this.message = faq;
        }
    }"
    class="assistant-widget z-50 hidden flex-col-reverse items-start gap-3 md:flex"
    @keydown.escape.window="open = false"
    @click.outside="open = false"
>
    <button
        type="button"
        aria-label="Open Gownsea assistant"
        class="assistant-widget-trigger group"
        @click="open = !open"
        :class="{ 'is-open': open }"
        :aria-expanded="open"
        aria-controls="gownsea-assistant-panel"
    >
        <span class="assistant-widget-trigger__shell"></span>
        <span class="assistant-widget-trigger__core">
            <svg x-show="!open" viewBox="0 0 24 24" fill="none" class="h-5 w-5 text-white" aria-hidden="true">
                <path d="M12 4.75c4.41 0 7.98 2.96 7.98 6.62 0 3.66-3.57 6.63-7.98 6.63-.79 0-1.56-.1-2.28-.3l-3.95 1.48 1.27-2.93C5.78 15.05 4.02 13.34 4.02 11.37c0-3.66 3.57-6.62 7.98-6.62Z" stroke="currentColor" stroke-width="1.55" stroke-linejoin="round"/>
                <circle cx="9.25" cy="11.37" r="0.95" fill="currentColor"/>
                <circle cx="12" cy="11.37" r="0.95" fill="currentColor"/>
                <circle cx="14.75" cy="11.37" r="0.95" fill="currentColor"/>
            </svg>
            <svg x-cloak x-show="open" viewBox="0 0 16 16" fill="none" class="h-4 w-4 text-white" aria-hidden="true">
                <path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </span>
        <span class="assistant-widget-trigger__dot" aria-hidden="true"></span>
    </button>

    <div
        id="gownsea-assistant-panel"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-3"
        class="assistant-panel"
        role="dialog"
        aria-labelledby="gownsea-assistant-title"
    >
        <div class="assistant-panel__header">
            <div class="assistant-panel__identity">
                <span class="assistant-panel__avatar" aria-hidden="true">G</span>
                <div>
                    <h3 id="gownsea-assistant-title" class="assistant-panel__title">Ask Gownsea Assistant</h3>
                    <p class="assistant-panel__status">Nairobi team · usually replies today</p>
                </div>
            </div>
            <button type="button" class="assistant-panel__close" @click="open = false" aria-label="Close assistant">
                <svg viewBox="0 0 16 16" fill="none" class="h-3.5 w-3.5" aria-hidden="true">
                    <path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="assistant-panel__body">
            @if ($whatsapp)
                <a
                    :href="'https://wa.me/{{ $whatsapp }}?text=' + encodeURIComponent(message || 'Hello Gownsea, I need help with ceremonial attire.')"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="assistant-panel__whatsapp"
                >
                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4 shrink-0" aria-hidden="true">
                        <path d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.02Zm-7.01 15.24h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.42 5.83c0 4.55-3.7 8.23-8.25 8.23Zm4.52-6.16c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.35-.76-1.84-.2-.48-.4-.42-.56-.42h-.48c-.17 0-.43.06-.66.31-.23.25-.87.85-.87 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.11-.23-.17-.48-.29Z"/>
                    </svg>
                    <span>
                        <strong>Chat on WhatsApp</strong>
                        <em>Fastest way to get sizing, hire, or bulk help</em>
                    </span>
                </a>
            @endif

            <p class="assistant-panel__label">Quick questions</p>
            <div class="assistant-panel__topics">
                <template x-for="[label, faq] in Object.entries(faqs)" :key="label">
                    <button
                        type="button"
                        class="assistant-panel__topic"
                        :class="{ 'is-active': topic === label }"
                        @click="pick(label, faq)"
                        x-text="label"
                    ></button>
                </template>
            </div>

            <form method="POST" action="{{ route('assistant.submit') }}" class="assistant-panel__form">
                @csrf
                <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
                <input type="text" name="company" class="hidden" tabindex="-1" autocomplete="off">
                <input type="hidden" name="form_token" value="{{ \App\Support\InquiryFormGuard::token() }}">
                <div class="assistant-panel__fields">
                    <input required name="name" type="text" class="assistant-panel__input" placeholder="Name" autocomplete="name">
                    <input required name="email" type="email" class="assistant-panel__input" placeholder="Email" autocomplete="email">
                </div>
                <input required name="phone" type="tel" class="assistant-panel__input" placeholder="Phone" autocomplete="tel">
                <textarea required name="message" x-model="message" rows="3" class="assistant-panel__input assistant-panel__input--area" placeholder="How can we help with hire, purchase, or bulk orders?"></textarea>
                <button type="submit" class="assistant-panel__submit">Send message</button>
            </form>
        </div>
    </div>
</div>
