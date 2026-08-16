@extends('layouts.admin')
@section('title', $customer->name)
@section('content')
    <div class="flex items-start justify-between"><div><h1>{{ $customer->name }}</h1><p class="text-sm">{{ $customer->email }} · {{ $customer->phone }}</p></div><x-admin.btn :href="route('admin.customers.edit', $customer)" variant="navy" icon="edit">Edit</x-admin.btn></div>
    <div class="mt-6 grid gap-4 md:grid-cols-4">
        <article class="admin-card">Inquiries<br><strong>{{ $customer->inquiries->count() }}</strong></article>
        <article class="admin-card">Leads<br><strong>{{ $customer->leads->count() }}</strong></article>
        <article class="admin-card">Sales<br><strong>{{ $customer->sales->count() }}</strong></article>
        <article class="admin-card">Spend<br><strong>KES {{ number_format($spend) }}</strong></article>
    </div>
    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        <div class="admin-card"><h2>Inquiries</h2><ul class="mt-3 space-y-2 text-sm">@foreach($customer->inquiries as $inquiry)<li><a href="{{ route('admin.inquiries.show', $inquiry) }}">#{{ $inquiry->id }}</a> {{ $inquiry->status }}</li>@endforeach</ul></div>
        <div class="admin-card"><h2>Leads</h2><ul class="mt-3 space-y-2 text-sm">@foreach($customer->leads as $lead)<li><a href="{{ route('admin.leads.show', $lead) }}">{{ $lead->reference }}</a> {{ $lead->stage }}</li>@endforeach</ul></div>
        <div class="admin-card"><h2>Sales</h2><ul class="mt-3 space-y-2 text-sm">@foreach($customer->sales as $sale)<li><a href="{{ route('admin.sales.show', $sale) }}">{{ $sale->number }}</a> KES {{ number_format($sale->total) }}</li>@endforeach</ul></div>
        <div class="admin-card"><h2>Activities</h2><ul class="mt-3 space-y-2 text-sm">@foreach($customer->activities as $activity)<li>{{ $activity->title }} · {{ $activity->created_at->format('d M Y') }}</li>@endforeach</ul></div>
    </div>
@endsection
