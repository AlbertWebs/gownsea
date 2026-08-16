@extends('layouts.admin')
@section('title', 'Leads')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1>Leads</h1>
            <p class="text-sm text-zinc-600">Manage prospects and track opportunities through the sales pipeline.</p>
        </div>
        <div class="flex gap-2">
            <a class="btn-secondary" href="{{ route('admin.leads.pipeline') }}">Pipeline</a>
            <a class="btn-secondary" href="{{ route('admin.leads.export') }}">Export CSV</a>
            <a class="btn-primary" href="{{ route('admin.leads.create') }}">Add Lead</a>
        </div>
    </div>
    @if(session('warnings'))
        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm">Possible duplicate: {{ implode(' ', session('warnings')) }} Submit again with confirmation if you still want to create it.</div>
    @endif
    <form class="mt-6 grid gap-3 md:grid-cols-6" method="GET">
        <input class="admin-input" name="q" value="{{ request('q') }}" placeholder="Search leads...">
        <select class="admin-input" name="stage"><option value="">Stage</option>@foreach(config('admin.lead_stages') as $stage)<option value="{{ $stage }}" @selected(request('stage')===$stage)>{{ str_replace('_',' ',$stage) }}</option>@endforeach</select>
        <select class="admin-input" name="source"><option value="">Source</option>@foreach(config('admin.sources') as $source)<option value="{{ $source }}" @selected(request('source')===$source)>{{ $source }}</option>@endforeach</select>
        <select class="admin-input" name="scope"><option value="">All leads</option><option value="mine" @selected(request('scope')==='mine')>My leads</option><option value="unassigned" @selected(request('scope')==='unassigned')>Unassigned</option></select>
        <button class="btn-secondary">Filter</button>
    </form>
    <div class="mt-6 overflow-x-auto rounded-2xl border border-zinc-200 bg-white">
        <table class="admin-table min-w-[900px]">
            <thead><tr><th>Lead</th><th>Contact</th><th>Product</th><th>Value</th><th>Stage</th><th>Assigned</th><th>Follow-up</th></tr></thead>
            <tbody>
            @forelse ($leads as $lead)
                <tr>
                    <td><a class="font-semibold text-[#d42127]" href="{{ route('admin.leads.show', $lead) }}">{{ $lead->reference }}</a><div>{{ $lead->name }}</div></td>
                    <td>{{ $lead->email }}<div class="text-xs">{{ $lead->phone }}</div></td>
                    <td>{{ $lead->product?->name ?? '—' }}</td>
                    <td>KES {{ number_format($lead->estimated_value) }}</td>
                    <td><span class="admin-badge">{{ str_replace('_',' ',$lead->stage) }}</span></td>
                    <td>{{ $lead->assignee?->name ?? 'Unassigned' }}</td>
                    <td>{{ optional($lead->next_follow_up_at)->format('d M Y') ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-8 text-center text-zinc-500">No leads have been added yet. <a class="text-[#d42127]" href="{{ route('admin.leads.create') }}">Add Lead</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $leads->links() }}</div>
@endsection
