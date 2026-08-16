@extends('layouts.admin')
@section('title', $sale->number)
@section('content')
    <h1>{{ $sale->number }}</h1>
    <p class="text-sm text-zinc-600">{{ $sale->customer?->name }} · {{ $sale->salesperson?->name }}</p>
    <form class="admin-card mt-6 max-w-lg space-y-3" method="POST" action="{{ route('admin.sales.update', $sale) }}">
        @csrf @method('PUT')
        <select class="admin-input" name="status">@foreach(config('admin.sale_statuses') as $status)<option value="{{ $status }}" @selected($sale->status===$status)>{{ $status }}</option>@endforeach</select>
        <select class="admin-input" name="payment_status">@foreach(config('admin.payment_statuses') as $status)<option value="{{ $status }}" @selected($sale->payment_status===$status)>{{ $status }}</option>@endforeach</select>
        <textarea class="admin-input" name="notes" rows="3">{{ $sale->notes }}</textarea>
        <button class="btn-primary">Update sale</button>
    </form>
    <div class="admin-card mt-6">
        <table class="admin-table">
            <thead><tr><th>Item</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
            <tbody>
            @foreach ($sale->items as $item)
                <tr><td>{{ $item->name }}</td><td>{{ $item->quantity }}</td><td>{{ number_format($item->unit_price) }}</td><td>{{ number_format($item->line_total) }}</td></tr>
            @endforeach
            </tbody>
        </table>
        <p class="mt-4 text-right font-semibold">Grand total KES {{ number_format($sale->total) }}</p>
    </div>
@endsection
