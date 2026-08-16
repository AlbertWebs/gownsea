@props([
    'name',
    'value' => '',
])

<div class="admin-editor" x-data="richEditor()">
    <textarea class="sr-only" name="{{ $name }}" x-ref="input">{{ $value }}</textarea>
    <div class="admin-editor__surface" x-ref="editor"></div>
</div>
