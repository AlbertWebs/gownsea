@extends('layouts.admin')
@section('title', $sale->number)
@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs text-zinc-500">Dashboard / Sales / {{ $sale->number }}</p>
            <h1>{{ $sale->number }}</h1>
            <p class="mt-1 text-sm text-zinc-600">{{ $sale->customer?->name ?? 'No customer' }} · {{ $sale->salesperson?->name }}</p>
        </div>
        <x-admin.btn :href="route('admin.sales.index')" variant="ghost" icon="back">Back to sales</x-admin.btn>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <section class="admin-card">
            <h2>Line items</h2>
            <div class="mt-4 overflow-hidden rounded-xl border border-zinc-200">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td class="font-semibold text-zinc-900">{{ $item->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price) }}</td>
                            <td class="font-semibold">{{ number_format($item->line_total) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-4 text-right font-semibold">Grand total KES {{ number_format($sale->total) }}</p>
        </section>
        <aside class="lg:sticky lg:top-24 self-start">
            <form class="admin-card space-y-4" method="POST" action="{{ route('admin.sales.update', $sale) }}">
                @csrf @method('PUT')
                <h2>Manage</h2>
                <label class="block text-sm font-semibold">Status
                    <select class="admin-input mt-2" name="status">@foreach(config('admin.sale_statuses') as $status)<option value="{{ $status }}" @selected($sale->status===$status)>{{ str_replace('_',' ', $status) }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Payment
                    <select class="admin-input mt-2" name="payment_status">@foreach(config('admin.payment_statuses') as $status)<option value="{{ $status }}" @selected($sale->payment_status===$status)>{{ str_replace('_',' ', $status) }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Notes
                    <textarea class="admin-input mt-2" name="notes" rows="4">{{ $sale->notes }}</textarea>
                </label>
                <x-admin.btn class="w-full" icon="save">Update sale</x-admin.btn>
            </form>
        </aside>
    </div>
@endsection
