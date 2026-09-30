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
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.02Z"/></svg>
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
            <svg x-show="!open" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.05 4.91A9.82 9.82 0 0 0 12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.87 9.87 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.91-7.02Z"/></svg>
            <svg x-cloak x-show="open" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
            <span class="whatsapp-support-trigger__tooltip" aria-hidden="true">Chat with us</span>
        </button>
    </div>
@endif
