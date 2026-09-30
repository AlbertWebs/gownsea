@php
    $whatsapp = preg_replace('/\D+/', '', (string) config('gownsea.brand.whatsapp'));
@endphp

@if ($whatsapp)
    <div
        x-data="{
            open: false,
            message: '',
            toggle() {
                this.open = ! this.open;
                if (this.open) this.$nextTick(() => this.$refs.message?.focus());
            },
            send() {
                const message = this.message.trim();
                if (! message) return;
                window.open('https://wa.me/{{ $whatsapp }}?text=' + encodeURIComponent(message), '_blank', 'noopener,noreferrer');
                this.message = '';
                this.open = false;
            }
        }"
        class="whatsapp-support-widget"
        @keydown.escape.window="open = false"
        @click.outside="open = false"
    >
        <section
            id="whatsapp-support-popup"
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="whatsapp-support-popup"
            role="dialog"
            aria-label="WhatsApp chat with Gownsea"
            @click.stop
        >
            <header class="whatsapp-support-popup__header">
                <span class="whatsapp-support-popup__avatar" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.02Zm-7.01 15.24h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.42 5.83c0 4.55-3.7 8.23-8.25 8.23Zm4.52-6.16c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.35-.76-1.84-.2-.48-.4-.42-.56-.42h-.48c-.17 0-.43.06-.66.31-.23.25-.87.85-.87 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.11-.23-.17-.48-.29Z"/></svg>
                    <i aria-hidden="true"></i>
                </span>
                <span class="whatsapp-support-popup__identity">
                    <strong>Gownsea Support</strong>
                    <small>Typically replies today</small>
                </span>
                <button type="button" class="whatsapp-support-popup__close" @click="open = false" aria-label="Close WhatsApp chat">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
            </header>

            <div class="whatsapp-support-popup__body">
                <p>Hello! How can we help you with graduation, legal, or church attire today?</p>
            </div>

            <form class="whatsapp-support-popup__form" @submit.prevent="send()">
                <label class="sr-only" for="whatsapp-support-message">Your message</label>
                <textarea
                    id="whatsapp-support-message"
                    x-ref="message"
                    x-model="message"
                    rows="2"
                    required
                    placeholder="Type your message here..."
                ></textarea>
                <button type="submit" aria-label="Send WhatsApp message">
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.894 2.553a1 1 0 0 0-1.788 0l-7 14a1 1 0 0 0 1.169 1.409l5-1.429A1 1 0 0 0 9 15.571V11a1 1 0 1 1 2 0v4.571a1 1 0 0 0 .725.962l5 1.428a1 1 0 0 0 1.17-1.408l-7-14Z"/></svg>
                </button>
                <div class="whatsapp-support-popup__meta">
                    <span>Direct chat via WhatsApp</span>
                    <span><i aria-hidden="true"></i> Online</span>
                </div>
            </form>
        </section>

        <button
            type="button"
            class="whatsapp-support-trigger"
            @click="toggle()"
            :aria-expanded="open"
            aria-controls="whatsapp-support-popup"
            :aria-label="open ? 'Close WhatsApp chat' : 'Open WhatsApp chat'"
            title="Chat with Gownsea on WhatsApp"
        >
            <svg x-show="!open" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.02Zm-7.01 15.24h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.18 8.18 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.42 5.83c0 4.55-3.7 8.23-8.25 8.23Zm4.52-6.16c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.35-.76-1.84-.2-.48-.4-.42-.56-.42h-.48c-.17 0-.43.06-.66.31-.23.25-.87.85-.87 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.11-.23-.17-.48-.29Z"/></svg>
            <svg x-cloak x-show="open" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
            <span class="whatsapp-support-trigger__tooltip" aria-hidden="true">Chat with us</span>
        </button>
    </div>
@endif
