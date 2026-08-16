@extends('layouts.admin')
@section('title', $lead->exists ? 'Edit lead' : 'Add lead')
@section('content')
    <form id="lead-form" class="space-y-6" method="POST" action="{{ $lead->exists ? route('admin.leads.update', $lead) : route('admin.leads.store') }}" x-data="{ tab: 'details' }">
        @csrf
        @if($lead->exists) @method('PUT') @endif
        @if(session('warnings') || !empty($warnings ?? []))
            <input type="hidden" name="confirm_duplicate" value="1">
        @endif

        <x-admin.form-header
            crumb="Dashboard / CRM / Leads / {{ $lead->exists ? 'Edit' : 'Create' }}"
            :title="$lead->exists ? 'Edit lead' : 'Add lead'"
            description="Track an opportunity through the sales pipeline."
            :cancel="route('admin.leads.index')"
            :submit="$lead->exists ? 'Save changes' : 'Create lead'"
        />

        @if(session('warnings') || !empty($warnings ?? []))
            <p class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ implode(' ', session('warnings', $warnings ?? [])) }} Submit again to create it anyway.</p>
        @endif

        <div class="flex flex-wrap gap-2 border-b border-zinc-200 pb-2 text-sm">
            <button type="button" class="rounded-full px-4 py-2" :class="tab === 'details' ? 'bg-[#0f2744] text-white' : 'bg-zinc-100 text-zinc-700'" @click="tab = 'details'">Contact</button>
            <button type="button" class="rounded-full px-4 py-2" :class="tab === 'opportunity' ? 'bg-[#0f2744] text-white' : 'bg-zinc-100 text-zinc-700'" @click="tab = 'opportunity'">Opportunity</button>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div class="space-y-6">
                <section class="admin-card space-y-4" x-show="tab === 'details'">
                    <div>
                        <h2>Contact</h2>
                        <p class="mt-1 text-sm text-zinc-500">Who this opportunity belongs to.</p>
                    </div>
                    <label class="block text-sm font-semibold">Name <span class="text-[#d42127]">*</span>
                        <input class="admin-input mt-2" name="name" value="{{ old('name', $lead->name) }}" required>
                    </label>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm font-semibold">Email
                            <input class="admin-input mt-2" type="email" name="email" value="{{ old('email', $lead->email) }}">
                        </label>
                        <label class="block text-sm font-semibold">Phone
                            <input class="admin-input mt-2" name="phone" value="{{ old('phone', $lead->phone) }}">
                        </label>
                    </div>
                    <label class="block text-sm font-semibold">Company
                        <input class="admin-input mt-2" name="company" value="{{ old('company', $lead->company) }}">
                    </label>
                    <label class="block text-sm font-semibold">Source
                        <select class="admin-input mt-2" name="source">@foreach(config('admin.sources') as $source)<option value="{{ $source }}" @selected(old('source', $lead->source)===$source)>{{ str_replace('_',' ', $source) }}</option>@endforeach</select>
                    </label>
                </section>
                <section class="admin-card space-y-4" x-show="tab === 'opportunity'">
                    <div>
                        <h2>Opportunity</h2>
                        <p class="mt-1 text-sm text-zinc-500">Value, product interest and follow-up.</p>
                    </div>
                    <label class="block text-sm font-semibold">Interested product
                        <select class="admin-input mt-2" name="product_id"><option value="">None</option>@foreach($products as $product)<option value="{{ $product->id }}" @selected(old('product_id', $lead->product_id)==$product->id)>{{ $product->name }}</option>@endforeach</select>
                    </label>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block text-sm font-semibold">Estimated value (KES)
                            <input class="admin-input mt-2" type="number" name="estimated_value" value="{{ old('estimated_value', $lead->estimated_value) }}">
                        </label>
                        <label class="block text-sm font-semibold">Probability %
                            <input class="admin-input mt-2" type="number" name="probability" min="0" max="100" value="{{ old('probability', $lead->probability) }}">
                        </label>
                    </div>
                    <label class="block text-sm font-semibold">Next follow-up
                        <input class="admin-input mt-2" type="datetime-local" name="next_follow_up_at" value="{{ old('next_follow_up_at', optional($lead->next_follow_up_at)->format('Y-m-d\TH:i')) }}">
                    </label>
                    <label class="block text-sm font-semibold">Notes
                        <textarea class="admin-input mt-2" name="notes" rows="4">{{ old('notes', $lead->notes) }}</textarea>
                    </label>
                </section>
            </div>
            <aside class="admin-card space-y-4 lg:sticky lg:top-24 self-start">
                <h2>Assignment</h2>
                <label class="block text-sm font-semibold">Assigned to
                    <select class="admin-input mt-2" name="assigned_to"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected(old('assigned_to', $lead->assigned_to)==$user->id)>{{ $user->name }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Priority
                    <select class="admin-input mt-2" name="priority">@foreach(['low','normal','high','urgent'] as $priority)<option value="{{ $priority }}" @selected(old('priority', $lead->priority)===$priority)>{{ ucfirst($priority) }}</option>@endforeach</select>
                </label>
                @if($lead->exists)
                    <label class="block text-sm font-semibold">Stage
                        <select class="admin-input mt-2" name="stage">@foreach(config('admin.lead_stages') as $stage)<option value="{{ $stage }}" @selected($lead->stage===$stage)>{{ str_replace('_',' ',$stage) }}</option>@endforeach</select>
                    </label>
                @endif
                <x-admin.btn class="w-full" icon="save">{{ $lead->exists ? 'Save changes' : 'Create lead' }}</x-admin.btn>
            </aside>
        </div>
    </form>
@endsection
