@extends('layouts.admin')
@section('title', 'Create sale')
@section('content')
    <h1>Create sale</h1>
    <form class="admin-card mt-6 max-w-3xl space-y-4" method="POST" action="{{ route('admin.sales.store') }}" x-data="{ items: [{ product_id: '', quantity: 1, unit_price: 0 }] }">
        @csrf
        <label class="block text-sm font-semibold">Existing customer
            <select class="admin-input mt-2" name="customer_id"><option value="">Create new below</option>@foreach($customers as $customer)<option value="{{ $customer->id }}">{{ $customer->name }}</option>@endforeach</select>
        </label>
        <div class="grid gap-4 md:grid-cols-3">
            <input class="admin-input" name="new_customer_name" placeholder="New customer name">
            <input class="admin-input" name="new_customer_email" placeholder="Email">
            <input class="admin-input" name="new_customer_phone" placeholder="Phone">
        </div>
        <label class="block text-sm font-semibold">Salesperson
            <select class="admin-input mt-2" name="salesperson_id">@foreach($users as $user)<option value="{{ $user->id }}" @selected($user->id===auth()->id())>{{ $user->name }}</option>@endforeach</select>
        </label>
        <div class="grid gap-4 md:grid-cols-2">
            <select class="admin-input" name="status">@foreach(config('admin.sale_statuses') as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach</select>
            <select class="admin-input" name="payment_status">@foreach(config('admin.payment_statuses') as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach</select>
        </div>
        <template x-for="(item, index) in items" :key="index">
            <div class="grid gap-2 md:grid-cols-3">
                <select class="admin-input" :name="'items['+index+'][product_id]'" x-model="item.product_id">
                    <option value="">Product</option>
                    @foreach($products as $product)<option value="{{ $product->id }}" data-price="{{ $product->price_amount }}">{{ $product->name }}</option>@endforeach
                </select>
                <input class="admin-input" type="number" min="1" :name="'items['+index+'][quantity]'" x-model="item.quantity">
                <input class="admin-input" type="number" min="0" :name="'items['+index+'][unit_price]'" x-model="item.unit_price">
            </div>
        </template>
        <button type="button" class="text-sm text-[#d42127]" @click="items.push({ product_id: '', quantity: 1, unit_price: 0 })">Add line</button>
        <label class="block text-sm font-semibold">Discount<input class="admin-input mt-2" type="number" name="discount" value="0"></label>
        <textarea class="admin-input" name="notes" rows="3" placeholder="Notes"></textarea>
        <button class="btn-primary">Save sale</button>
    </form>
@endsection
