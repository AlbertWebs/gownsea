@extends('layouts.admin')
@section('title', 'Users')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1>Users</h1>
            <p class="text-sm text-zinc-600">People who can sign in to this admin panel.</p>
        </div>
        <x-admin.btn :href="route('admin.users.create')" icon="user">Add user</x-admin.btn>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table min-w-[720px]">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last login</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>
                        <span class="font-semibold text-zinc-900">{{ $user->name }}</span>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td><x-admin.badge :status="str_replace('_', ' ', $user->role)" /></td>
                    <td><x-admin.badge :status="$user->status" /></td>
                    <td>{{ optional($user->last_login_at)->format('d M Y H:i') ?? '—' }}</td>
                    <td>
                        <div class="flex justify-end">
                            <a class="btn-navy btn-sm" href="{{ route('admin.users.edit', $user) }}"><x-admin.icon name="edit" /> Edit</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="admin-table__empty">
                        <p>No users yet</p>
                        <p><a class="font-semibold text-[#d42127]" href="{{ route('admin.users.create') }}">Add a user</a>.</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
@endsection
