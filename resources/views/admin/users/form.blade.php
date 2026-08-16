@extends('layouts.admin')
@section('title', $user->exists ? 'Edit user' : 'Add user')
@section('content')
    <h1>{{ $user->exists ? 'Edit user' : 'Add user' }}</h1>
    <form class="admin-card mt-6 max-w-xl space-y-4" method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if($user->exists) @method('PUT') @endif
        <label class="block text-sm font-semibold">Name<input class="admin-input mt-2" name="name" value="{{ old('name', $user->name) }}" required></label>
        <label class="block text-sm font-semibold">Email<input class="admin-input mt-2" type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
        <label class="block text-sm font-semibold">Phone<input class="admin-input mt-2" name="phone" value="{{ old('phone', $user->phone) }}"></label>
        <label class="block text-sm font-semibold">Role
            <select class="admin-input mt-2" name="role">@foreach($roles as $role)<option value="{{ $role }}" @selected(old('role', $user->role)===$role)>{{ str_replace('_',' ',$role) }}</option>@endforeach</select>
        </label>
        <label class="block text-sm font-semibold">Status
            <select class="admin-input mt-2" name="status"><option value="active" @selected($user->status==='active')>active</option><option value="disabled" @selected($user->status==='disabled')>disabled</option></select>
        </label>
        <label class="block text-sm font-semibold">Password @if($user->exists)<span class="font-normal text-zinc-500">(leave blank to keep)</span>@endif
            <input class="admin-input mt-2" type="password" name="password" {{ $user->exists ? '' : 'required' }}>
        </label>
        <button class="btn-primary">Save user</button>
    </form>
@endsection
