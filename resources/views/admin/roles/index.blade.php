@extends('layouts.admin')
@section('title', 'Roles')
@section('content')
    <h1>Roles & permissions</h1>
    <p class="text-sm text-zinc-600">Permissions are enforced on every admin route, not only in the sidebar.</p>
    <div class="mt-6 grid gap-4 md:grid-cols-2">
        @foreach ($roles as $role => $permissions)
            <article class="admin-card">
                <h2>{{ str_replace('_',' ', $role) }}</h2>
                <p class="mt-2 text-sm text-zinc-600">{{ $permissions === ['*'] ? 'Full system access' : implode(', ', $permissions) }}</p>
            </article>
        @endforeach
    </div>
@endsection
