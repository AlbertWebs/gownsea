@extends('layouts.admin')
@section('title', 'Blogs & Articles')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-zinc-500">Dashboard / Content</p>
            <h1>Blogs &amp; Articles</h1>
            <p class="mt-1 text-sm text-zinc-600">Write and publish stories for The Gown Journal.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-admin.btn :href="route('admin.journal.images')" variant="secondary" icon="image">Image library</x-admin.btn>
            <x-admin.btn :href="route('admin.journal.create')" icon="plus">New article</x-admin.btn>
        </div>
    </div>

    <div class="mt-6 grid gap-3 sm:grid-cols-3">
        <article class="admin-card py-4"><p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">All articles</p><p class="mt-1 text-2xl font-semibold">{{ $counts['all'] }}</p></article>
        <article class="admin-card py-4"><p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Published</p><p class="mt-1 text-2xl font-semibold">{{ $counts['published'] }}</p></article>
        <article class="admin-card py-4"><p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Drafts</p><p class="mt-1 text-2xl font-semibold">{{ $counts['draft'] }}</p></article>
    </div>

    <form class="mt-6 flex flex-wrap items-center gap-3" method="GET">
        <input class="admin-input min-w-0 flex-1 !w-auto" name="q" value="{{ request('q') }}" placeholder="Search titles or slugs...">
        <select class="admin-input w-44" name="status"><option value="">All statuses</option><option value="published" @selected(request('status')==='published')>Published</option><option value="draft" @selected(request('status')==='draft')>Draft</option></select>
        <x-admin.btn variant="violet" icon="filter">Filter</x-admin.btn>
        @if(request()->hasAny(['q','status']))<x-admin.btn :href="route('admin.journal.index')" variant="ghost" icon="x">Clear</x-admin.btn>@endif
    </form>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Article</th><th class="hidden md:table-cell">Category</th><th>Status</th><th class="hidden md:table-cell">Updated</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
            @forelse($posts as $post)
                <tr>
                    <td><a class="font-semibold text-zinc-900 hover:text-[#d42127]" href="{{ route('admin.journal.edit', $post) }}">{{ $post->title }}</a><p class="mt-0.5 font-mono text-xs text-zinc-500">/{{ $post->slug }}</p></td>
                    <td class="hidden text-zinc-600 md:table-cell">{{ $post->category }}</td>
                    <td><span class="admin-badge {{ $post->status === 'published' ? 'admin-badge--ok' : '' }}">{{ ucfirst($post->status) }}</span></td>
                    <td class="hidden text-sm text-zinc-500 md:table-cell">{{ $post->updated_at->format('M j, Y') }}</td>
                    <td><div class="flex items-center justify-end gap-2 whitespace-nowrap">
                        @if($post->status === 'published')<a class="btn-ghost btn-sm" href="{{ route('journal.show', $post->slug) }}" target="_blank" rel="noopener">View</a>@endif
                        <a class="btn-navy btn-sm" href="{{ route('admin.journal.edit', $post) }}"><x-admin.icon name="edit" /> Edit</a>
                        <form method="POST" action="{{ route('admin.journal.destroy', $post) }}" onsubmit="return confirm('Delete this article?')">@csrf @method('DELETE')<button class="btn-danger btn-sm" type="submit"><x-admin.icon name="trash" /> Delete</button></form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="admin-table__empty"><p class="text-base font-semibold text-zinc-800">No articles yet</p><p class="mt-1 text-sm text-zinc-500">Create a draft or publish your first Gown Journal article.</p><a class="mt-3 inline-flex font-semibold text-[#d42127]" href="{{ route('admin.journal.create') }}">Write an article →</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $posts->links() }}</div>
@endsection
