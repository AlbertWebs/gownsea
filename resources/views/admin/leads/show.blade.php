@extends('layouts.admin')
@section('title', $lead->reference)
@section('content')
    <p class="text-xs text-zinc-500">Dashboard / Leads / {{ $lead->reference }}</p>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1>{{ $lead->name }}</h1>
            <p class="text-sm text-zinc-600">{{ $lead->reference }} · Weighted forecast KES {{ number_format($lead->weightedForecast()) }}</p>
        </div>
        <form method="POST" action="{{ route('admin.leads.convert-sale', $lead) }}">@csrf<button class="btn-primary">Convert to sale</button></form>
    </div>
    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <form class="admin-card space-y-3 lg:col-span-1" method="POST" action="{{ route('admin.leads.update', $lead) }}">
            @csrf @method('PUT')
            <input type="hidden" name="name" value="{{ $lead->name }}">
            <input type="hidden" name="source" value="{{ $lead->source }}">
            <label class="block text-sm font-semibold">Stage
                <select class="admin-input mt-2" name="stage">@foreach($stages as $stage)<option value="{{ $stage }}" @selected($lead->stage===$stage)>{{ str_replace('_',' ',$stage) }}</option>@endforeach</select>
            </label>
            <label class="block text-sm font-semibold">Assigned to
                <select class="admin-input mt-2" name="assigned_to"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected($lead->assigned_to==$user->id)>{{ $user->name }}</option>@endforeach</select>
            </label>
            <label class="block text-sm font-semibold">Estimated value<input class="admin-input mt-2" type="number" name="estimated_value" value="{{ $lead->estimated_value }}"></label>
            <label class="block text-sm font-semibold">Probability<input class="admin-input mt-2" type="number" name="probability" value="{{ $lead->probability }}"></label>
            <button class="btn-primary w-full">Update lead</button>
        </form>
        <div class="admin-card lg:col-span-2">
            <h2>Timeline</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @forelse ($lead->activities as $activity)
                    <li><strong>{{ $activity->title }}</strong> · {{ $activity->created_at->format('d M Y H:i') }}<div class="text-zinc-600">{{ $activity->description }}</div></li>
                @empty
                    <li class="text-zinc-500">No activity recorded yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
