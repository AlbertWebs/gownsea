@extends('layouts.admin')
@section('title', $customer->exists ? 'Edit customer' : 'Add customer')
@section('content')
    <form class="space-y-6" method="POST" action="{{ $customer->exists ? route('admin.customers.update', $customer) : route('admin.customers.store') }}">
        @csrf
        @if($customer->exists) @method('PUT') @endif

        <x-admin.form-header
            crumb="Dashboard / Sales / Customers / {{ $customer->exists ? 'Edit' : 'Create' }}"
            :title="$customer->exists ? 'Edit customer' : 'Add customer'"
            description="Customers are matched automatically from inquiries by email or phone."
            :cancel="route('admin.customers.index')"
            :submit="$customer->exists ? 'Save changes' : 'Create customer'"
        />

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <section class="admin-card space-y-4">
                <div>
                    <h2>Customer details</h2>
                    <p class="mt-1 text-sm text-zinc-500">Contact information used across inquiries, leads and sales.</p>
                </div>
                <label class="block text-sm font-semibold">Name <span class="text-[#d42127]">*</span>
                    <input class="admin-input mt-2" name="name" value="{{ old('name', $customer->name) }}" required>
                </label>
                <label class="block text-sm font-semibold">Company
                    <input class="admin-input mt-2" name="company" value="{{ old('company', $customer->company) }}">
                </label>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block text-sm font-semibold">Email
                        <input class="admin-input mt-2" type="email" name="email" value="{{ old('email', $customer->email) }}">
                    </label>
                    <label class="block text-sm font-semibold">Phone
                        <input class="admin-input mt-2" name="phone" value="{{ old('phone', $customer->phone) }}">
                    </label>
                </div>
                <label class="block text-sm font-semibold">Address
                    <textarea class="admin-input mt-2" name="address" rows="3">{{ old('address', $customer->address) }}</textarea>
                </label>
                <label class="block text-sm font-semibold">Notes
                    <textarea class="admin-input mt-2" name="notes" rows="4">{{ old('notes', $customer->notes) }}</textarea>
                </label>
            </section>
            <aside class="admin-card space-y-4 lg:sticky lg:top-24 self-start">
                <h2>Status</h2>
                <label class="block text-sm font-semibold">Account status
                    <select class="admin-input mt-2" name="status">
                        <option value="active" @selected(old('status', $customer->status)==='active')>Active</option>
                        <option value="inactive" @selected(old('status', $customer->status)==='inactive')>Inactive</option>
                    </select>
                </label>
                <x-admin.btn class="w-full" icon="save">{{ $customer->exists ? 'Save changes' : 'Create customer' }}</x-admin.btn>
            </aside>
        </div>
    </form>
@endsection
