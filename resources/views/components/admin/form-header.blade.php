@props([
    'crumb' => '',
    'title',
    'description' => '',
    'cancel' => null,
    'submit' => 'Save',
])

<div class="flex flex-wrap items-start justify-between gap-4">
    <div>
        @if($crumb)
            <p class="text-xs text-zinc-500">{{ $crumb }}</p>
        @endif
        <h1>{{ $title }}</h1>
        @if($description)
            <p class="mt-1 text-sm text-zinc-600">{{ $description }}</p>
        @endif
    </div>
    <div class="flex flex-wrap items-center gap-2">
        @if($cancel)
            <x-admin.btn :href="$cancel" variant="ghost" icon="x">Cancel</x-admin.btn>
        @endif
        <x-admin.btn icon="save">{{ $submit }}</x-admin.btn>
    </div>
</div>
