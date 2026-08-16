@php
    $clients = config('gownsea.clients', []);
@endphp

@if (count($clients))
    <section class="container-shell section-md">
        <x-ui.section-header
            kicker="Our Clients"
            title="Trusted by institutions across Kenya"
            description="A growing network of universities, colleges, and organizations rely on Gownsea for dependable ceremonial attire."
        />

        <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            @foreach ($clients as $index => $client)
                <div class="surface flex min-h-24 items-center justify-center p-4">
                    <img
                        src="{{ $client }}"
                        alt="Gownsea client {{ $index + 1 }}"
                        class="max-h-16 w-auto object-contain"
                        loading="lazy"
                    >
                </div>
            @endforeach
        </div>
    </section>
@endif
