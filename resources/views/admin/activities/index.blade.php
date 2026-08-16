@extends('layouts.admin')
@section('title', 'Activities')
@section('content')
    <form class="space-y-6" method="POST" action="{{ route('admin.activities.store') }}">
        @csrf
        <x-admin.form-header
            crumb="Dashboard / CRM / Activities"
            title="Activities / Follow-ups"
            description="Log calls, meetings and reminders against a lead."
            submit="Add activity"
        />

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <section class="admin-card space-y-4">
                <div>
                    <h2>New activity</h2>
                    <p class="mt-1 text-sm text-zinc-500">Overdue pending items appear on the dashboard.</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block text-sm font-semibold">Type
                        <select class="admin-input mt-2" name="type">@foreach(config('admin.activity_types') as $type)<option value="{{ $type }}">{{ str_replace('_',' ',$type) }}</option>@endforeach</select>
                    </label>
                    <label class="block text-sm font-semibold">Status
                        <select class="admin-input mt-2" name="status">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </label>
                </div>
                <label class="block text-sm font-semibold">Related lead
                    <select class="admin-input mt-2" name="lead_id"><option value="">None</option>@foreach($leads as $lead)<option value="{{ $lead->id }}">{{ $lead->name }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Due at
                    <input class="admin-input mt-2" type="datetime-local" name="due_at">
                </label>
                <label class="block text-sm font-semibold">Description <span class="text-[#d42127]">*</span>
                    <textarea class="admin-input mt-2" name="description" rows="4" required></textarea>
                </label>
            </section>
            <aside class="admin-card space-y-4 lg:sticky lg:top-24 self-start">
                <h2>Save</h2>
                <p class="text-sm text-zinc-500">Use pending for follow-ups that still need action.</p>
                <x-admin.btn class="w-full" icon="plus">Add activity</x-admin.btn>
            </aside>
        </div>
    </form>

    <div class="admin-table-wrap mt-8">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Lead</th>
                    <th>Due</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($activities as $activity)
                <tr>
                    <td class="font-semibold text-zinc-900">{{ $activity->title }}</td>
                    <td>{{ $activity->lead?->name ?? '—' }}</td>
                    <td>{{ optional($activity->due_at)->format('d M Y H:i') ?? '—' }}</td>
                    <td>
                        <x-admin.badge :status="$activity->isOverdue() ? 'overdue' : $activity->status" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="admin-table__empty">
                        <p>No activities yet</p>
                        <p>Add a follow-up using the form above.</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $activities->links() }}</div>
@endsection
