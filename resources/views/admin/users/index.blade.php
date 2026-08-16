@extends('layouts.admin')
@section('title', 'Users')
@section('content')
    <div class="flex items-end justify-between"><h1>Users</h1><a class="btn-primary" href="{{ route('admin.users.create') }}">Add user</a></div>
    <div class="mt-6 overflow-x-auto rounded-2xl border border-zinc-200 bg-white">
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last login</th><th></th></tr></thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ str_replace('_',' ', $user->role) }}</td>
                    <td>{{ $user->status }}</td>
                    <td>{{ optional($user->last_login_at)->format('d M Y H:i') ?? '—' }}</td>
                    <td><a href="{{ route('admin.users.edit', $user) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->links() }}</div>
@endsection
