@extends('layouts.public')

@section('content')
    <section class="container-shell section-lg">
        <x-ui.section-header
            kicker="Privacy"
            title="Privacy Policy"
            description="We collect only the details required to process orders and support requests. Data is handled securely and never sold to third parties."
        />
        <div class="mt-10 surface border-l-4 border-l-[#0f2744] p-6 md:p-8">
            <p class="text-sm leading-relaxed text-zinc-600">Questions about this policy can be sent to the Nairobi team via the contact page.</p>
        </div>
    </section>
@endsection
