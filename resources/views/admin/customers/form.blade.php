@extends('layouts.admin')
@section('title', $customer->exists ? 'Edit customer' : 'Add customer')
@section('content')
    <h1>{{ $customer->exists ? 'Edit customer' : 'Add customer' }}</h1>
    <form class="admin-card mt-6 max-w-xl space-y-4" method="POST" action="{{ $customer->exists ? route('admin.customers.update', $customer) : route('admin.customers.store') }}">
        @csrf
        @if($customer->exists) @method('PUT') @endif
        <label class="block text-sm font-semibold">Name *<input class="admin-input mt-2" name="name" value="{{ old('name', $customer->name) }}" required></label>
        <label class="block text-sm font-semibold">Company<input class="admin-input mt-2" name="company" value="{{ old('company', $customer->company) }}"></label>
        <label class="block text-sm font-semibold">Email<input class="admin-input mt-2" type="email" name="email" value="{{ old('email', $customer->email) }}"></label>
        <label class="block text-sm font-semibold">Phone<input class="admin-input mt-2" name="phone" value="{{ old('phone', $customer->phone) }}"></label>
        <label class="block text-sm font-semibold">Address<textarea class="admin-input mt-2" name="address" rows="2">{{ old('address', $customer->address) }}</textarea></label>
        <select class="admin-input" name="status"><option value="active" @selected($customer->status==='active')>active</option><option value="inactive" @selected($customer->status==='inactive')>inactive</option></select>
        <textarea class="admin-input" name="notes" rows="3" placeholder="Notes">{{ old('notes', $customer->notes) }}</textarea>
        <button class="btn-primary">Save</button>
    </form>
@endsection
