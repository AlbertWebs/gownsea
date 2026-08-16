@extends('layouts.admin')
@section('title', 'Inquiry #'.$inquiry->id)
@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs text-zinc-500">Dashboard / CRM / Inquiries / #{{ $inquiry->id }}</p>
            <h1>Inquiry #{{ $inquiry->id }}</h1>
            <p class="mt-1 text-sm text-zinc-600">{{ $inquiry->type === 'product' ? 'Product enquiry' : 'General enquiry' }} · {{ $inquiry->created_at->format('d M Y H:i') }}</p>
        </div>
        @if(!$inquiry->lead_id)
            <form method="POST" action="{{ route('admin.inquiries.convert', $inquiry) }}">@csrf<x-admin.btn icon="convert">Convert to lead</x-admin.btn></form>
        @else
            <x-admin.btn :href="route('admin.leads.show', $inquiry->lead_id)" variant="navy" icon="eye">View lead</x-admin.btn>
        @endif
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
        <div class="space-y-6">
            <section class="admin-card space-y-3">
                <h2>Customer</h2>
                <p class="text-sm text-zinc-700">{{ $inquiry->name }}<br>{{ $inquiry->email }}<br>{{ $inquiry->phone }}</p>
            </section>
            @if($inquiry->product)
                <section class="admin-card space-y-3">
                    <h2>Product</h2>
                    <p class="text-sm">{{ $inquiry->product->name }} · {{ $inquiry->product->displayPrice() }}</p>
                </section>
            @endif
            <section class="admin-card space-y-3">
                <h2>Message</h2>
                <p class="whitespace-pre-wrap text-sm text-zinc-700">{{ $inquiry->message }}</p>
            </section>
            <section class="admin-card">
                <h2>Timeline</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    @forelse ($inquiry->activities as $activity)
                        <li><strong>{{ $activity->title }}</strong> · {{ $activity->created_at->format('d M Y H:i') }}<div class="text-zinc-600">{{ $activity->description }}</div></li>
                    @empty
                        <li class="text-zinc-500">No activity recorded yet.</li>
                    @endforelse
                </ul>
            </section>
        </div>
        <aside class="space-y-4 lg:sticky lg:top-24 self-start">
            <form class="admin-card space-y-4" method="POST" action="{{ route('admin.inquiries.update', $inquiry) }}">
                @csrf @method('PATCH')
                <h2>Manage</h2>
                <label class="block text-sm font-semibold">Status
                    <select class="admin-input mt-2" name="status">@foreach(config('admin.inquiry_statuses') as $status)<option value="{{ $status }}" @selected($inquiry->status===$status)>{{ str_replace('_',' ', $status) }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Priority
                    <select class="admin-input mt-2" name="priority">@foreach(['low','normal','high','urgent'] as $priority)<option value="{{ $priority }}" @selected($inquiry->priority===$priority)>{{ ucfirst($priority) }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Assigned to
                    <select class="admin-input mt-2" name="assigned_to"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected($inquiry->assigned_to==$user->id)>{{ $user->name }}</option>@endforeach</select>
                </label>
                <x-admin.btn class="w-full" icon="save">Save</x-admin.btn>
            </form>
            <form class="admin-card space-y-4" method="POST" action="{{ route('admin.inquiries.note', $inquiry) }}">
                @csrf
                <h2>Internal note</h2>
                <textarea class="admin-input" name="description" rows="4" required></textarea>
                <x-admin.btn class="w-full" variant="navy" icon="note">Add note</x-admin.btn>
            </form>
        </aside>
    </div>
@endsection
