@extends('layouts.admin')
@section('title', 'Sales')
@section('content')
    <div class="flex items-end justify-between gap-4">
        <div><h1>Sales</h1><p class="text-sm text-zinc-600">Quotations and completed orders. There is no public checkout; sales are entered here.</p></div>
        <div class="flex gap-2"><a class="btn-secondary" href="{{ route('admin.sales.export') }}">Export CSV</a><a class="btn-primary" href="{{ route('admin.sales.create') }}">Create sale</a></div>
    </div>
    <form class="mt-6 grid gap-3 md:grid-cols-4" method="GET">
        <input class="admin-input" name="q" value="{{ request('q') }}" placeholder="Search sales...">
        <select class="admin-input" name="status"><option value="">Status</option>@foreach(config('admin.sale_statuses') as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select>
        <select class="admin-input" name="payment_status"><option value="">Payment</option>@foreach(config('admin.payment_statuses') as $status)<option value="{{ $status }}" @selected(request('payment_status')===$status)>{{ $status }}</option>@endforeach</select>
        <button class="btn-secondary">Filter</button>
    </form>
    <div class="mt-6 overflow-x-auto rounded-2xl border border-zinc-200 bg-white">
        <table class="admin-table min-w-[800px]">
            <thead><tr><th>Number</th><th>Customer</th><th>Total</th><th>Status</th><th>Payment</th><th>Date</th></tr></thead>
            <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td><a class="text-[#d42127]" href="{{ route('admin.sales.show', $sale) }}">{{ $sale->number }}</a></td>
                    <td>{{ $sale->customer?->name ?? '—' }}</td>
                    <td>KES {{ number_format($sale->total) }}</td>
                    <td>{{ $sale->status }}</td>
                    <td>{{ $sale->payment_status }}</td>
                    <td>{{ $sale->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-zinc-500">No sales have been recorded yet. <a class="text-[#d42127]" href="{{ route('admin.sales.create') }}">Create sale</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sales->links() }}</div>
@endsection
