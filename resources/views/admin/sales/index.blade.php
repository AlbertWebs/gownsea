@extends('layouts.admin')
@section('title', 'Sales')
@section('content')
    <div class="flex items-end justify-between gap-4">
        <div><h1>Sales</h1><p class="text-sm text-zinc-600">Quotations and completed orders. There is no public checkout; sales are entered here.</p></div>
        <div class="flex gap-2">
            <x-admin.btn :href="route('admin.sales.export')" variant="teal" icon="download">Export CSV</x-admin.btn>
            <x-admin.btn :href="route('admin.sales.create')" icon="plus">Create sale</x-admin.btn>
        </div>
    </div>
    <form class="mt-6 flex flex-nowrap items-center gap-3" method="GET">
        <input class="admin-input min-w-0 flex-1 !w-auto" name="q" value="{{ request('q') }}" placeholder="Search sales...">
        <select class="admin-input w-40 shrink-0 !w-40" name="status"><option value="">Status</option>@foreach(config('admin.sale_statuses') as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select>
        <select class="admin-input w-40 shrink-0 !w-40" name="payment_status"><option value="">Payment</option>@foreach(config('admin.payment_statuses') as $status)<option value="{{ $status }}" @selected(request('payment_status')===$status)>{{ $status }}</option>@endforeach</select>
        <x-admin.btn class="shrink-0" variant="violet" icon="filter">Filter</x-admin.btn>
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table min-w-[880px]">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td><a class="admin-table__name" href="{{ route('admin.sales.show', $sale) }}">{{ $sale->number }}</a></td>
                    <td>{{ $sale->customer?->name ?? '—' }}</td>
                    <td class="font-semibold text-zinc-900">KES {{ number_format($sale->total) }}</td>
                    <td><x-admin.badge :status="$sale->status" /></td>
                    <td><x-admin.badge :status="$sale->payment_status" /></td>
                    <td>{{ $sale->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex justify-end">
                            <a class="btn-navy btn-sm" href="{{ route('admin.sales.show', $sale) }}"><x-admin.icon name="eye" /> View</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="admin-table__empty">
                        <p>No sales yet</p>
                        <p><a class="font-semibold text-[#d42127]" href="{{ route('admin.sales.create') }}">Create a sale</a> to record a quotation or order.</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sales->links() }}</div>
@endsection
