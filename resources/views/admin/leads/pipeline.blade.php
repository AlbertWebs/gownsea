@extends('layouts.admin')
@section('title', 'Pipeline')
@section('content')
    <div class="flex items-end justify-between">
        <div><h1>Lead pipeline</h1><p class="text-sm text-zinc-600">Drag cards between stages on desktop.</p></div>
        <a class="btn-secondary" href="{{ route('admin.leads.index') }}">List view</a>
    </div>
    <div class="mt-6 flex gap-4 overflow-x-auto pb-4">
        @foreach ($stages as $stage)
            <section class="w-64 shrink-0 rounded-2xl border border-zinc-200 bg-zinc-50 p-3" data-stage="{{ $stage }}">
                <h2 class="text-base">{{ str_replace('_',' ', $stage) }} ({{ ($grouped[$stage] ?? collect())->count() }})</h2>
                <div class="mt-3 space-y-3 min-h-24" ondragover="event.preventDefault()" ondrop="dropLead(event, '{{ $stage }}')">
                    @foreach (($grouped[$stage] ?? collect()) as $lead)
                        <article draggable="true" ondragstart="event.dataTransfer.setData('id', '{{ $lead->id }}')" class="rounded-xl border border-zinc-200 bg-white p-3 text-sm">
                            <a class="font-semibold" href="{{ route('admin.leads.show', $lead) }}">{{ $lead->name }}</a>
                            <p class="text-zinc-500">{{ $lead->product?->name }}</p>
                            <p>KES {{ number_format($lead->estimated_value) }}</p>
                            <p class="text-xs">{{ $lead->assignee?->name ?? 'Unassigned' }} · {{ $lead->priority }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
    <script>
        function dropLead(event, stage) {
            event.preventDefault();
            const id = event.dataTransfer.getData('id');
            fetch(@json(url('/admin/leads')) + '/' + id + '/move', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ stage })
            }).then(() => location.reload());
        }
    </script>
@endsection
