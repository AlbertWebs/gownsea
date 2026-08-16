@extends('layouts.admin')
@section('title', 'Customers')
@section('content')
    <div class="flex items-end justify-between"><div><h1>Customers</h1></div><div class="flex gap-2"><a class="btn-secondary" href="{{ route('admin.customers.export') }}">Export CSV</a><a class="btn-primary" href="{{ route('admin.customers.create') }}">Add customer</a></div></div>
    <form class="mt-6" method="GET"><input class="admin-input max-w-md" name="q" value="{{ request('q') }}" placeholder="Search customers..."></form>
    <div class="mt-6 overflow-x-auto rounded-2xl border border-zinc-200 bg-white">
        <table class="admin-table min-w-[800px]">
            <thead><tr><th>Name</th><th>Contact</th><th>Inquiries</th><th>Leads</th><th>Sales</th></tr></thead>
            <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td><a class="text-[#d42127]" href="{{ route('admin.customers.show', $customer) }}">{{ $customer->name }}</a><div class="text-xs">{{ $customer->company }}</div></td>
                    <td>{{ $customer->email }}<div class="text-xs">{{ $customer->phone }}</div></td>
                    <td>{{ $customer->inquiries_count }}</td>
                    <td>{{ $customer->leads_count }}</td>
                    <td>{{ $customer->sales_count }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-8 text-center text-zinc-500">No customers have been added yet. <a class="text-[#d42127]" href="{{ route('admin.customers.create') }}">Add customer</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $customers->links() }}</div>
@endsection
