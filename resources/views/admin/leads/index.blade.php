@extends('layouts.admin')
@section('title', 'Leads')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1>Leads</h1>
            <p class="text-sm text-zinc-600">Manage prospects and track opportunities through the sales pipeline.</p>
        </div>
        <div class="flex gap-2">
            <x-admin.btn :href="route('admin.leads.pipeline')" variant="navy" icon="kanban">Pipeline</x-admin.btn>
            <x-admin.btn :href="route('admin.leads.export')" variant="teal" icon="download">Export CSV</x-admin.btn>
            <x-admin.btn :href="route('admin.leads.create')" icon="plus">Add Lead</x-admin.btn>
        </div>
    </div>
    @if(session('warnings'))
        <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm">Possible duplicate: {{ implode(' ', session('warnings')) }} Submit again with confirmation if you still want to create it.</div>
    @endif
    <form class="mt-6 flex flex-nowrap items-center gap-3" method="GET">
        <input class="admin-input min-w-0 flex-1 !w-auto" name="q" value="{{ request('q') }}" placeholder="Search leads...">
        <select class="admin-input w-40 shrink-0 !w-40" name="stage"><option value="">Stage</option>@foreach(config('admin.lead_stages') as $stage)<option value="{{ $stage }}" @selected(request('stage')===$stage)>{{ str_replace('_',' ',$stage) }}</option>@endforeach</select>
        <select class="admin-input w-40 shrink-0 !w-40" name="source"><option value="">Source</option>@foreach(config('admin.sources') as $source)<option value="{{ $source }}" @selected(request('source')===$source)>{{ $source }}</option>@endforeach</select>
        <select class="admin-input w-40 shrink-0 !w-40" name="scope"><option value="">All leads</option><option value="mine" @selected(request('scope')==='mine')>My leads</option><option value="unassigned" @selected(request('scope')==='unassigned')>Unassigned</option></select>
        <x-admin.btn class="shrink-0" variant="violet" icon="filter">Filter</x-admin.btn>
    </form>
    <div class="admin-table-wrap">
        <table class="admin-table min-w-[960px]">
            <thead>
                <tr>
                    <th>Lead</th>
                    <th>Contact</th>
                    <th>Product</th>
                    <th>Value</th>
                    <th>Stage</th>
                    <th>Assigned</th>
                    <th>Follow-up</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($leads as $lead)
                <tr>
                    <td>
                        <a class="admin-table__name" href="{{ route('admin.leads.show', $lead) }}">{{ $lead->name }}</a>
                        <p class="admin-table__meta">{{ $lead->reference }}</p>
                    </td>
                    <td>
                        {{ $lead->email ?: '—' }}
                        <p class="admin-table__meta">{{ $lead->phone ?: '—' }}</p>
                    </td>
                    <td>{{ $lead->product?->name ?? '—' }}</td>
                    <td class="font-semibold text-zinc-900">KES {{ number_format($lead->estimated_value) }}</td>
                    <td><x-admin.badge :status="$lead->stage" /></td>
                    <td>{{ $lead->assignee?->name ?? 'Unassigned' }}</td>
                    <td>{{ optional($lead->next_follow_up_at)->format('d M Y') ?? '—' }}</td>
                    <td>
                        <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                            <a class="btn-navy btn-sm" href="{{ route('admin.leads.edit', $lead) }}"><x-admin.icon name="edit" /> Edit</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="admin-table__empty">
                        <p>No leads yet</p>
                        <p><a class="font-semibold text-[#d42127]" href="{{ route('admin.leads.create') }}">Add a lead</a> to start tracking opportunities.</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $leads->links() }}</div>
@endsection
