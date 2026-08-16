@extends('layouts.admin')
@section('title', 'Search')
@section('content')
    <h1>Search</h1>
    <form class="mt-4" method="GET"><input class="admin-input max-w-xl" name="q" value="{{ $q }}" placeholder="Search..."></form>
    @if($q)
        @foreach (['products' => 'Products', 'customers' => 'Customers', 'leads' => 'Leads', 'inquiries' => 'Inquiries', 'sales' => 'Sales'] as $key => $label)
            <section class="admin-card mt-4">
                <h2>{{ $label }}</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    @forelse (${$key} as $item)
                        <li>
                            @if($key==='products')<a href="{{ route('admin.catalogue.products.show', $item) }}">{{ $item->name }}</a>
                            @elseif($key==='customers')<a href="{{ route('admin.customers.show', $item) }}">{{ $item->name }} {{ $item->phone }}</a>
                            @elseif($key==='leads')<a href="{{ route('admin.leads.show', $item) }}">{{ $item->name }} {{ $item->phone }}</a>
                            @elseif($key==='inquiries')<a href="{{ route('admin.inquiries.show', $item) }}">{{ $item->name }} {{ $item->phone }}</a>
                            @else<a href="{{ route('admin.sales.show', $item) }}">{{ $item->number }}</a>
                            @endif
                        </li>
                    @empty
                        <li class="text-zinc-500">No matching {{ strtolower($label) }}.</li>
                    @endforelse
                </ul>
            </section>
        @endforeach
    @endif
@endsection
