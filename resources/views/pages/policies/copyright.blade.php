@extends('layouts.public')

@section('content')
    <section class="container-shell section-lg">
        <x-ui.section-header
            kicker="Copyright"
            title="Copyright Statement"
            description="All text, images, logos, and brand materials on this website are protected by copyright law and require prior written permission for reuse."
        />
        <div class="mt-10 surface border-l-4 border-l-[#0f2744] p-6 md:p-8">
            <p class="text-sm leading-relaxed text-zinc-600">Questions about this policy can be sent to the Nairobi team via the contact page.</p>
        </div>
    </section>
@endsection
