@extends('layouts.public')

@section('content')
    <section class="container-shell section-lg">
        <x-ui.section-header
            kicker="Institutional Services"
            title="Bulk Hire"
            description="Request tailored pricing for graduation gowns and accessories. Share your quantities and timelines and we'll follow up quickly."
        />

        <div class="surface mt-10 p-6 md:p-8">
            <h3 class="font-semibold">Request bulk pricing</h3>
            <form method="POST" action="{{ route('assistant.submit') }}" class="mt-4 grid gap-4 md:grid-cols-2">
                @csrf
                <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
                <input required name="name" class="rounded-lg border border-zinc-300 px-3 py-2 text-sm" type="text" placeholder="Name">
                <input required name="email" class="rounded-lg border border-zinc-300 px-3 py-2 text-sm" type="email" placeholder="Email">
                <input required name="phone" class="rounded-lg border border-zinc-300 px-3 py-2 text-sm md:col-span-2" type="text" placeholder="Mobile">
                <textarea required name="message" class="rounded-lg border border-zinc-300 px-3 py-2 text-sm md:col-span-2" rows="4" placeholder="Company/Institution, quantity, timeline, and items needed"></textarea>
                <button type="submit" class="btn-primary md:col-span-2">Send bulk inquiry</button>
            </form>
        </div>
    </section>
@endsection
