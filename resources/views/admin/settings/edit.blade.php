@extends('layouts.admin')
@section('title', 'Website settings')
@section('content')
    <h1>Website settings</h1>
    <form class="admin-card mt-6 max-w-xl space-y-4" method="POST" action="{{ route('admin.settings.update') }}">
        @csrf @method('PUT')
        @foreach ($settings as $key => $value)
            <label class="block text-sm font-semibold">{{ str_replace('_',' ', $key) }}
                @if($key === 'seo_description')
                    <textarea class="admin-input mt-2" name="{{ $key }}" rows="3">{{ $value }}</textarea>
                @else
                    <input class="admin-input mt-2" name="{{ $key }}" value="{{ $value }}">
                @endif
            </label>
        @endforeach
        <button class="btn-primary">Save settings</button>
    </form>
@endsection
