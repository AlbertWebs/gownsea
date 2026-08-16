@extends('layouts.admin')
@section('title', $lead->reference)
@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs text-zinc-500">Dashboard / CRM / Leads / {{ $lead->reference }}</p>
            <h1>{{ $lead->name }}</h1>
            <p class="mt-1 text-sm text-zinc-600">{{ $lead->reference }} · Weighted forecast KES {{ number_format($lead->weightedForecast()) }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-admin.btn :href="route('admin.leads.edit', $lead)" variant="navy" icon="edit">Edit lead</x-admin.btn>
            <form method="POST" action="{{ route('admin.leads.convert-sale', $lead) }}">@csrf<x-admin.btn icon="convert">Convert to sale</x-admin.btn></form>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <section class="admin-card">
            <h2>Timeline</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @forelse ($lead->activities as $activity)
                    <li><strong>{{ $activity->title }}</strong> · {{ $activity->created_at->format('d M Y H:i') }}<div class="text-zinc-600">{{ $activity->description }}</div></li>
                @empty
                    <li class="text-zinc-500">No activity recorded yet.</li>
                @endforelse
            </ul>
        </section>
        <aside class="lg:sticky lg:top-24 self-start">
            <form class="admin-card space-y-4" method="POST" action="{{ route('admin.leads.update', $lead) }}">
                @csrf @method('PUT')
                <h2>Manage</h2>
                <input type="hidden" name="name" value="{{ $lead->name }}">
                <input type="hidden" name="source" value="{{ $lead->source }}">
                <label class="block text-sm font-semibold">Stage
                    <select class="admin-input mt-2" name="stage">@foreach($stages as $stage)<option value="{{ $stage }}" @selected($lead->stage===$stage)>{{ str_replace('_',' ',$stage) }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Assigned to
                    <select class="admin-input mt-2" name="assigned_to"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected($lead->assigned_to==$user->id)>{{ $user->name }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Estimated value
                    <input class="admin-input mt-2" type="number" name="estimated_value" value="{{ $lead->estimated_value }}">
                </label>
                <label class="block text-sm font-semibold">Probability
                    <input class="admin-input mt-2" type="number" name="probability" value="{{ $lead->probability }}">
                </label>
                <x-admin.btn class="w-full" icon="save">Update lead</x-admin.btn>
            </form>
        </aside>
    </div>
@endsection
