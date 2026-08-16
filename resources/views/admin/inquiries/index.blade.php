@extends('layouts.admin')
@section('title', $type === 'product' ? 'Product inquiries' : 'General inquiries')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1>{{ $type === 'product' ? 'Product inquiries' : 'General inquiries' }}</h1>
            <p class="text-sm text-zinc-600">Every website form submission appears here.</p>
        </div>
        <a class="btn-secondary" href="{{ route('admin.inquiries.export', ['type' => $type]) }}">Export CSV</a>
    </div>
    <form class="mt-6 grid gap-3 md:grid-cols-5" method="GET">
        <input class="admin-input" name="q" value="{{ request('q') }}" placeholder="Search inquiries...">
        <select class="admin-input" name="status"><option value="">Status</option>@foreach(config('admin.inquiry_statuses') as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select>
        <select class="admin-input" name="assigned_to"><option value="">Assigned to</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(request('assigned_to')==$user->id)>{{ $user->name }}</option>@endforeach</select>
        <button class="btn-secondary">Filter</button>
    </form>
    <div class="mt-6 overflow-x-auto rounded-2xl border border-zinc-200 bg-white">
        <table class="admin-table min-w-[800px]">
            <thead><tr><th>ID</th><th>Customer</th><th>Product</th><th>Status</th><th>Assigned</th><th>Created</th></tr></thead>
            <tbody>
            @forelse ($inquiries as $inquiry)
                <tr>
                    <td><a class="text-[#d42127]" href="{{ route('admin.inquiries.show', $inquiry) }}">#{{ $inquiry->id }}</a></td>
                    <td>{{ $inquiry->name }}<div class="text-xs text-zinc-500">{{ $inquiry->email }} · {{ $inquiry->phone }}</div></td>
                    <td>{{ $inquiry->product?->name ?? '—' }}</td>
                    <td><span class="admin-badge">{{ $inquiry->status }}</span></td>
                    <td>{{ $inquiry->assignee?->name ?? 'Unassigned' }}</td>
                    <td>{{ $inquiry->created_at->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-zinc-500">No inquiries have been received yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $inquiries->links() }}</div>
@endsection
