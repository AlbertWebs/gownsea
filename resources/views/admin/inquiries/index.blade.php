@extends('layouts.admin')
@section('title', $type === 'product' ? 'Product inquiries' : 'General inquiries')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1>{{ $type === 'product' ? 'Product inquiries' : 'General inquiries' }}</h1>
            <p class="text-sm text-zinc-600">Every website form submission appears here.</p>
        </div>
        <x-admin.btn :href="route('admin.inquiries.export', ['type' => $type])" variant="teal" icon="download">Export CSV</x-admin.btn>
    </div>
    <form class="mt-6 flex flex-nowrap items-center gap-3" method="GET">
        <input class="admin-input min-w-0 flex-1 !w-auto" name="q" value="{{ request('q') }}" placeholder="Search inquiries...">
        <select class="admin-input w-40 shrink-0 !w-40" name="status"><option value="">Status</option>@foreach(config('admin.inquiry_statuses') as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select>
        <select class="admin-input w-44 shrink-0 !w-44" name="assigned_to"><option value="">Assigned to</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(request('assigned_to')==$user->id)>{{ $user->name }}</option>@endforeach</select>
        <x-admin.btn class="shrink-0" variant="violet" icon="filter">Filter</x-admin.btn>
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table min-w-[880px]">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Assigned</th>
                    <th>Created</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($inquiries as $inquiry)
                <tr>
                    <td><a class="admin-table__name" href="{{ route('admin.inquiries.show', $inquiry) }}">#{{ $inquiry->id }}</a></td>
                    <td>
                        {{ $inquiry->name }}
                        <p class="admin-table__meta">{{ $inquiry->email }} · {{ $inquiry->phone }}</p>
                    </td>
                    <td>{{ $inquiry->product?->name ?? '—' }}</td>
                    <td><x-admin.badge :status="$inquiry->status" /></td>
                    <td>{{ $inquiry->assignee?->name ?? 'Unassigned' }}</td>
                    <td>{{ $inquiry->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <div class="flex justify-end">
                            <a class="btn-navy btn-sm" href="{{ route('admin.inquiries.show', $inquiry) }}"><x-admin.icon name="eye" /> View</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="admin-table__empty">
                        <p>No inquiries yet</p>
                        <p>Website form submissions will appear here.</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $inquiries->links() }}</div>
@endsection
