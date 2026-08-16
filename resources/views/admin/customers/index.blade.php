@extends('layouts.admin')
@section('title', 'Customers')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1>Customers</h1>
            <p class="text-sm text-zinc-600">People matched from inquiries, leads and sales.</p>
        </div>
        <div class="flex gap-2">
            <x-admin.btn :href="route('admin.customers.export')" variant="teal" icon="download">Export CSV</x-admin.btn>
            <x-admin.btn :href="route('admin.customers.create')" icon="user">Add customer</x-admin.btn>
        </div>
    </div>
    <form class="mt-6 flex flex-nowrap items-center gap-3" method="GET">
        <input class="admin-input min-w-0 flex-1 !w-auto" name="q" value="{{ request('q') }}" placeholder="Search customers...">
        <x-admin.btn class="shrink-0" variant="violet" icon="filter">Filter</x-admin.btn>
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table min-w-[800px]">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Inquiries</th>
                    <th>Leads</th>
                    <th>Sales</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td>
                        <a class="admin-table__name" href="{{ route('admin.customers.show', $customer) }}">{{ $customer->name }}</a>
                        <p class="admin-table__meta">{{ $customer->company ?: '—' }}</p>
                    </td>
                    <td>
                        {{ $customer->email ?: '—' }}
                        <p class="admin-table__meta">{{ $customer->phone ?: '—' }}</p>
                    </td>
                    <td>{{ $customer->inquiries_count }}</td>
                    <td>{{ $customer->leads_count }}</td>
                    <td>{{ $customer->sales_count }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                            <a class="btn-navy btn-sm" href="{{ route('admin.customers.edit', $customer) }}"><x-admin.icon name="edit" /> Edit</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="admin-table__empty">
                        <p>No customers yet</p>
                        <p><a class="font-semibold text-[#d42127]" href="{{ route('admin.customers.create') }}">Add a customer</a>.</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $customers->links() }}</div>
@endsection
