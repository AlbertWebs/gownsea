@extends('layouts.admin')
@section('title', 'Inquiry #'.$inquiry->id)
@section('content')
    <p class="text-xs text-zinc-500">Dashboard / Inquiries / #{{ $inquiry->id }}</p>
    <h1>Inquiry #{{ $inquiry->id }}</h1>
    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <div class="admin-card lg:col-span-2 space-y-3">
            <h2>Customer</h2>
            <p>{{ $inquiry->name }}<br>{{ $inquiry->email }}<br>{{ $inquiry->phone }}</p>
            @if($inquiry->product)
                <h2 class="mt-6">Product</h2>
                <p>{{ $inquiry->product->name }} · {{ $inquiry->product->displayPrice() }}</p>
            @endif
            <h2 class="mt-6">Message</h2>
            <p class="whitespace-pre-wrap text-sm text-zinc-700">{{ $inquiry->message }}</p>
            <h2 class="mt-6">Timeline</h2>
            <ul class="mt-2 space-y-2 text-sm">
                @foreach ($inquiry->activities as $activity)
                    <li><strong>{{ $activity->title }}</strong> · {{ $activity->created_at->format('d M Y H:i') }}<div class="text-zinc-600">{{ $activity->description }}</div></li>
                @endforeach
            </ul>
        </div>
        <div class="space-y-4">
            <form class="admin-card space-y-3" method="POST" action="{{ route('admin.inquiries.update', $inquiry) }}">
                @csrf @method('PATCH')
                <label class="block text-sm font-semibold">Status
                    <select class="admin-input mt-2" name="status">@foreach(config('admin.inquiry_statuses') as $status)<option value="{{ $status }}" @selected($inquiry->status===$status)>{{ $status }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Priority
                    <select class="admin-input mt-2" name="priority">@foreach(['low','normal','high','urgent'] as $priority)<option value="{{ $priority }}" @selected($inquiry->priority===$priority)>{{ $priority }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Assigned to
                    <select class="admin-input mt-2" name="assigned_to"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected($inquiry->assigned_to==$user->id)>{{ $user->name }}</option>@endforeach</select>
                </label>
                <button class="btn-primary w-full">Save</button>
            </form>
            <form class="admin-card space-y-3" method="POST" action="{{ route('admin.inquiries.note', $inquiry) }}">
                @csrf
                <label class="block text-sm font-semibold">Internal note<textarea class="admin-input mt-2" name="description" rows="3" required></textarea></label>
                <button class="btn-secondary w-full">Add note</button>
            </form>
            @if(!$inquiry->lead_id)
                <form method="POST" action="{{ route('admin.inquiries.convert', $inquiry) }}">@csrf<button class="btn-primary w-full">Convert to lead</button></form>
            @else
                <a class="btn-secondary w-full" href="{{ route('admin.leads.show', $inquiry->lead_id) }}">View lead</a>
            @endif
        </div>
    </div>
@endsection
