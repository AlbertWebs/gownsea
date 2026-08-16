@extends('layouts.admin')
@section('title', $product->name)
@section('content')
    <p class="text-xs text-zinc-500">Dashboard / Catalogue / Products / {{ $product->name }}</p>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1>{{ $product->name }}</h1>
            <p class="text-sm text-zinc-600">{{ $product->displayPrice() }} · {{ $product->status }}</p>
        </div>
        <x-admin.btn :href="route('admin.catalogue.products.edit', $product)" variant="navy" icon="edit">Edit</x-admin.btn>
    </div>
    <div class="mt-6 grid gap-4 md:grid-cols-4">
        @foreach ([['Inquiries', $inquiryCount], ['Leads', $leadCount], ['Sales lines', $salesCount], ['Revenue', 'KES '.number_format($revenue)]] as [$label, $value])
            <article class="admin-card"><p class="text-xs text-zinc-500">{{ $label }}</p><p class="mt-1 text-xl font-semibold">{{ $value }}</p></article>
        @endforeach
    </div>
    <div class="admin-card mt-6">
        <h2>Recent inquiries</h2>
        <ul class="mt-4 space-y-2 text-sm">
            @forelse ($product->inquiries as $inquiry)
                <li><a class="text-[#d42127]" href="{{ route('admin.inquiries.show', $inquiry) }}">{{ $inquiry->name }}</a> · {{ $inquiry->created_at->format('d M Y') }}</li>
            @empty
                <li class="text-zinc-500">No inquiries for this product yet.</li>
            @endforelse
        </ul>
    </div>
@endsection
