@extends('layouts.admin')
@section('title', 'Reports')
@section('content')
    <h1>Reports</h1>
    <p class="text-sm text-zinc-600">Sales, lead and inquiry performance.</p>
    <div class="mt-6 grid gap-4 md:grid-cols-3">
        <article class="admin-card">Pipeline value<br><strong>KES {{ number_format($pipelineValue) }}</strong></article>
        <article class="admin-card">Weighted pipeline<br><strong>KES {{ number_format($weightedPipeline) }}</strong></article>
        <article class="admin-card">Won / Lost<br><strong>{{ $won }} / {{ $lost }}</strong></article>
    </div>
    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        <article class="admin-card">
            <h2>Most enquired products</h2>
            <ul class="mt-3 space-y-2 text-sm">@foreach($mostEnquired as $product)<li class="flex justify-between">{{ $product->name }}<span>{{ $product->inquiries_count }}</span></li>@endforeach</ul>
        </article>
        <article class="admin-card">
            <h2>Lead sources</h2>
            <ul class="mt-3 space-y-2 text-sm">@foreach($leadsBySource as $source => $total)<li class="flex justify-between">{{ $source }}<span>{{ $total }}</span></li>@endforeach</ul>
        </article>
        <article class="admin-card">
            <h2>Sales by product</h2>
            <ul class="mt-3 space-y-2 text-sm">@foreach($salesByProduct as $row)<li class="flex justify-between">{{ $row['name'] }}<span>KES {{ number_format($row['revenue']) }}</span></li>@endforeach</ul>
        </article>
        <article class="admin-card">
            <h2>Products with no inquiries</h2>
            <ul class="mt-3 space-y-2 text-sm">@forelse($noInquiries as $product)<li>{{ $product->name }}</li>@empty<li class="text-zinc-500">Every product has inquiries.</li>@endforelse</ul>
        </article>
    </div>
@endsection
