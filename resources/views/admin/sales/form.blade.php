@extends('layouts.admin')
@section('title', 'Create sale')
@section('content')
    <form class="space-y-6" method="POST" action="{{ route('admin.sales.store') }}" x-data="{ tab: 'customer', items: [{ product_id: '', quantity: 1, unit_price: 0 }] }">
        @csrf

        <x-admin.form-header
            crumb="Dashboard / Sales / Create"
            title="Create sale"
            description="Record a quotation or confirmed order. There is no public checkout."
            :cancel="route('admin.sales.index')"
            submit="Create sale"
        />

        <div class="flex flex-wrap gap-2 border-b border-zinc-200 pb-2 text-sm">
            <button type="button" class="rounded-2xl px-4 py-2" :class="tab === 'customer' ? 'bg-[#0f2744] text-white' : 'bg-zinc-100 text-zinc-700'" @click="tab = 'customer'">Customer</button>
            <button type="button" class="rounded-2xl px-4 py-2" :class="tab === 'items' ? 'bg-[#0f2744] text-white' : 'bg-zinc-100 text-zinc-700'" @click="tab = 'items'">Line items</button>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-6">
                <section class="admin-card space-y-4" x-show="tab === 'customer'">
                    <div>
                        <h2>Customer</h2>
                        <p class="mt-1 text-sm text-zinc-500">Select an existing customer or enter a new one.</p>
                    </div>
                    <label class="block text-sm font-semibold">Existing customer
                        <select class="admin-input mt-2" name="customer_id">
                            <option value="">Create new below</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="grid gap-4 md:grid-cols-3">
                        <label class="block text-sm font-semibold">New name
                            <input class="admin-input mt-2" name="new_customer_name">
                        </label>
                        <label class="block text-sm font-semibold">New email
                            <input class="admin-input mt-2" name="new_customer_email">
                        </label>
                        <label class="block text-sm font-semibold">New phone
                            <input class="admin-input mt-2" name="new_customer_phone">
                        </label>
                    </div>
                    <label class="block text-sm font-semibold">Notes
                        <textarea class="admin-input mt-2" name="notes" rows="4"></textarea>
                    </label>
                </section>
                <section class="admin-card space-y-4" x-show="tab === 'items'">
                    <div>
                        <h2>Line items</h2>
                        <p class="mt-1 text-sm text-zinc-500">Add products, quantities and unit prices in KES.</p>
                    </div>
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid gap-3 md:grid-cols-[1fr_100px_140px_auto]">
                            <select class="admin-input" :name="'items['+index+'][product_id]'" x-model="item.product_id">
                                <option value="">Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            <input class="admin-input" type="number" min="1" :name="'items['+index+'][quantity]'" x-model="item.quantity" placeholder="Qty">
                            <input class="admin-input" type="number" min="0" :name="'items['+index+'][unit_price]'" x-model="item.unit_price" placeholder="Unit price">
                            <button type="button" class="text-sm text-[#d42127]" @click="items.splice(index, 1)">Remove</button>
                        </div>
                    </template>
                    <button type="button" class="text-sm font-semibold text-[#d42127]" @click="items.push({ product_id: '', quantity: 1, unit_price: 0 })">Add line</button>
                    <label class="block text-sm font-semibold">Discount (KES)
                        <input class="admin-input mt-2" type="number" name="discount" value="0" min="0">
                    </label>
                </section>
            </div>
            <aside class="admin-card space-y-4 lg:sticky lg:top-24 self-start">
                <h2>Sale status</h2>
                <label class="block text-sm font-semibold">Salesperson
                    <select class="admin-input mt-2" name="salesperson_id">@foreach($users as $user)<option value="{{ $user->id }}" @selected($user->id===auth()->id())>{{ $user->name }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Status
                    <select class="admin-input mt-2" name="status">@foreach(config('admin.sale_statuses') as $status)<option value="{{ $status }}">{{ str_replace('_',' ', $status) }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Payment
                    <select class="admin-input mt-2" name="payment_status">@foreach(config('admin.payment_statuses') as $status)<option value="{{ $status }}">{{ str_replace('_',' ', $status) }}</option>@endforeach</select>
                </label>
                <x-admin.btn class="w-full" icon="save">Create sale</x-admin.btn>
            </aside>
        </div>
    </form>
@endsection
