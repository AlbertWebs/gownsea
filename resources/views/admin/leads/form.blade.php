@extends('layouts.admin')
@section('title', $lead->exists ? 'Edit lead' : 'Add lead')
@section('content')
    <h1>{{ $lead->exists ? 'Edit lead' : 'Add lead' }}</h1>
    @if(session('warnings') || !empty($warnings ?? []))
        <p class="mt-4 text-sm text-amber-700">{{ implode(' ', session('warnings', $warnings ?? [])) }}</p>
        <input type="hidden" form="lead-form" name="confirm_duplicate" value="1">
    @endif
    <form id="lead-form" class="admin-card mt-6 max-w-2xl space-y-4" method="POST" action="{{ $lead->exists ? route('admin.leads.update', $lead) : route('admin.leads.store') }}">
        @csrf
        @if($lead->exists) @method('PUT') @endif
        <label class="block text-sm font-semibold">Name *<input class="admin-input mt-2" name="name" value="{{ old('name', $lead->name) }}" required></label>
        <div class="grid gap-4 md:grid-cols-2">
            <label class="block text-sm font-semibold">Email<input class="admin-input mt-2" type="email" name="email" value="{{ old('email', $lead->email) }}"></label>
            <label class="block text-sm font-semibold">Phone<input class="admin-input mt-2" name="phone" value="{{ old('phone', $lead->phone) }}"></label>
        </div>
        <label class="block text-sm font-semibold">Company<input class="admin-input mt-2" name="company" value="{{ old('company', $lead->company) }}"></label>
        <label class="block text-sm font-semibold">Source
            <select class="admin-input mt-2" name="source">@foreach(config('admin.sources') as $source)<option value="{{ $source }}" @selected(old('source', $lead->source)===$source)>{{ $source }}</option>@endforeach</select>
        </label>
        <label class="block text-sm font-semibold">Product
            <select class="admin-input mt-2" name="product_id"><option value="">None</option>@foreach($products as $product)<option value="{{ $product->id }}" @selected(old('product_id', $lead->product_id)==$product->id)>{{ $product->name }}</option>@endforeach</select>
        </label>
        <div class="grid gap-4 md:grid-cols-3">
            <label class="block text-sm font-semibold">Estimated value<input class="admin-input mt-2" type="number" name="estimated_value" value="{{ old('estimated_value', $lead->estimated_value) }}"></label>
            <label class="block text-sm font-semibold">Probability %<input class="admin-input mt-2" type="number" name="probability" min="0" max="100" value="{{ old('probability', $lead->probability) }}"></label>
            <label class="block text-sm font-semibold">Priority
                <select class="admin-input mt-2" name="priority">@foreach(['low','normal','high','urgent'] as $priority)<option value="{{ $priority }}" @selected(old('priority', $lead->priority)===$priority)>{{ $priority }}</option>@endforeach</select>
            </label>
        </div>
        <label class="block text-sm font-semibold">Assigned to
            <select class="admin-input mt-2" name="assigned_to"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(old('assigned_to', $lead->assigned_to)==$user->id)>{{ $user->name }}</option>@endforeach</select>
        </label>
        @if($lead->exists)
            <label class="block text-sm font-semibold">Stage
                <select class="admin-input mt-2" name="stage">@foreach(config('admin.lead_stages') as $stage)<option value="{{ $stage }}" @selected($lead->stage===$stage)>{{ str_replace('_',' ',$stage) }}</option>@endforeach</select>
            </label>
        @endif
        <label class="block text-sm font-semibold">Notes<textarea class="admin-input mt-2" name="notes" rows="4">{{ old('notes', $lead->notes) }}</textarea></label>
        <button class="btn-primary">Save lead</button>
    </form>
@endsection
