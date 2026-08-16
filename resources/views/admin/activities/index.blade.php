@extends('layouts.admin')
@section('title', 'Activities')
@section('content')
    <h1>Activities / Follow-ups</h1>
    <form class="admin-card mt-6 max-w-2xl space-y-3" method="POST" action="{{ route('admin.activities.store') }}">
        @csrf
        <div class="grid gap-3 md:grid-cols-2">
            <select class="admin-input" name="type">@foreach(config('admin.activity_types') as $type)<option value="{{ $type }}">{{ str_replace('_',' ',$type) }}</option>@endforeach</select>
            <select class="admin-input" name="status"><option value="pending">pending</option><option value="completed">completed</option><option value="cancelled">cancelled</option></select>
        </div>
        <select class="admin-input" name="lead_id"><option value="">Related lead</option>@foreach($leads as $lead)<option value="{{ $lead->id }}">{{ $lead->name }}</option>@endforeach</select>
        <input class="admin-input" type="datetime-local" name="due_at">
        <textarea class="admin-input" name="description" rows="3" required placeholder="Description"></textarea>
        <button class="btn-primary">Add activity</button>
    </form>
    <div class="mt-6 overflow-x-auto rounded-2xl border border-zinc-200 bg-white">
        <table class="admin-table">
            <thead><tr><th>Type</th><th>Lead</th><th>Due</th><th>Status</th></tr></thead>
            <tbody>
            @forelse ($activities as $activity)
                <tr>
                    <td>{{ $activity->title }}</td>
                    <td>{{ $activity->lead?->name }}</td>
                    <td>{{ optional($activity->due_at)->format('d M Y H:i') }}</td>
                    <td>{{ $activity->isOverdue() ? 'overdue' : $activity->status }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-8 text-center text-zinc-500">No activities have been added yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $activities->links() }}</div>
@endsection
