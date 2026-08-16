@extends('layouts.admin')
@section('title', $user->exists ? 'Edit user' : 'Add user')
@section('content')
    <form class="space-y-6" method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if($user->exists) @method('PUT') @endif

        <x-admin.form-header
            crumb="Dashboard / Administration / Users / {{ $user->exists ? 'Edit' : 'Create' }}"
            :title="$user->exists ? 'Edit user' : 'Add user'"
            description="Roles control which admin pages this person can open."
            :cancel="route('admin.users.index')"
            :submit="$user->exists ? 'Save changes' : 'Create user'"
        />

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
            <section class="admin-card space-y-4">
                <div>
                    <h2>Profile</h2>
                    <p class="mt-1 text-sm text-zinc-500">Login and contact details.</p>
                </div>
                <label class="block text-sm font-semibold">Name <span class="text-[#d42127]">*</span>
                    <input class="admin-input mt-2" name="name" value="{{ old('name', $user->name) }}" required>
                </label>
                <label class="block text-sm font-semibold">Email <span class="text-[#d42127]">*</span>
                    <input class="admin-input mt-2" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </label>
                <label class="block text-sm font-semibold">Phone
                    <input class="admin-input mt-2" name="phone" value="{{ old('phone', $user->phone) }}">
                </label>
                <label class="block text-sm font-semibold">Password @if($user->exists)<span class="font-normal text-zinc-500">(leave blank to keep)</span>@endif
                    <input class="admin-input mt-2" type="password" name="password" {{ $user->exists ? '' : 'required' }}>
                </label>
            </section>
            <aside class="admin-card space-y-4 lg:sticky lg:top-24 self-start">
                <h2>Access</h2>
                <label class="block text-sm font-semibold">Role
                    <select class="admin-input mt-2" name="role">@foreach($roles as $role)<option value="{{ $role }}" @selected(old('role', $user->role)===$role)>{{ str_replace('_',' ',$role) }}</option>@endforeach</select>
                </label>
                <label class="block text-sm font-semibold">Status
                    <select class="admin-input mt-2" name="status">
                        <option value="active" @selected(old('status', $user->status)==='active')>Active</option>
                        <option value="disabled" @selected(old('status', $user->status)==='disabled')>Disabled</option>
                    </select>
                </label>
                <x-admin.btn class="w-full" icon="save">{{ $user->exists ? 'Save changes' : 'Create user' }}</x-admin.btn>
            </aside>
        </div>
    </form>
@endsection
