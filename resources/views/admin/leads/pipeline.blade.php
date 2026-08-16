@php
    $board = [];
    foreach ($stages as $stage) {
        $board[$stage] = ($grouped[$stage] ?? collect())->map(fn ($lead) => [
            'id' => $lead->id,
            'name' => $lead->name,
            'reference' => $lead->reference,
            'product' => $lead->product?->name,
            'value' => (int) $lead->estimated_value,
            'assignee' => $lead->assignee?->name,
            'priority' => $lead->priority ?: 'normal',
            'followUp' => optional($lead->next_follow_up_at)->format('d M'),
            'overdue' => $lead->next_follow_up_at && $lead->next_follow_up_at->isPast() && ! in_array($lead->stage, ['won', 'lost'], true),
            'url' => route('admin.leads.show', $lead),
        ])->values();
    }
@endphp

@extends('layouts.admin')
@section('title', 'Pipeline')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-zinc-500">Dashboard / CRM / Pipeline</p>
            <h1>Lead pipeline</h1>
            <p class="mt-1 text-sm text-zinc-600">Drag cards between stages on desktop. On mobile, use Move on a card.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-admin.btn :href="route('admin.leads.index')" variant="navy" icon="list">List view</x-admin.btn>
            <x-admin.btn :href="route('admin.leads.create')" icon="plus">Add lead</x-admin.btn>
        </div>
    </div>

    <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="admin-card py-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Open leads</p>
            <p class="mt-1 text-2xl font-semibold">{{ $stats['open'] }}</p>
        </article>
        <article class="admin-card py-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Pipeline value</p>
            <p class="mt-1 text-2xl font-semibold">KES {{ number_format($stats['value']) }}</p>
        </article>
        <article class="admin-card py-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Weighted</p>
            <p class="mt-1 text-2xl font-semibold">KES {{ number_format($stats['weighted']) }}</p>
        </article>
        <article class="admin-card py-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Won</p>
            <p class="mt-1 text-2xl font-semibold">{{ $stats['won'] }}</p>
        </article>
    </div>

    <form class="mt-6 flex flex-nowrap items-center gap-3" method="GET">
        <input class="admin-input min-w-0 flex-1 !w-auto" name="q" value="{{ request('q') }}" placeholder="Search leads...">
        <select class="admin-input w-40 shrink-0 !w-40" name="source">
            <option value="">All sources</option>
            @foreach (config('admin.sources') as $source)
                <option value="{{ $source }}" @selected(request('source')===$source)>{{ str_replace('_', ' ', $source) }}</option>
            @endforeach
        </select>
        <select class="admin-input w-44 shrink-0 !w-44" name="assigned_to">
            <option value="">Anyone</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(request('assigned_to')==$user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        <x-admin.btn class="shrink-0" variant="violet" icon="filter">Filter</x-admin.btn>
        @if(request()->hasAny(['q', 'source', 'assigned_to']))
            <x-admin.btn class="shrink-0" :href="route('admin.leads.pipeline')" variant="ghost" icon="x">Clear</x-admin.btn>
        @endif
    </form>

    <div
        class="mt-6"
        x-data="pipelineBoard({
            moveUrl: @js(url('/admin/leads')),
            stages: @js($stages),
            columns: @js($board),
        })"
    >
        <p x-show="error" x-cloak class="mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" x-text="error"></p>

        <div class="admin-pipeline">
            @foreach ($stages as $stage)
                <section
                    class="admin-pipeline__col admin-pipeline__col--{{ $stage }}"
                    :class="overStage === '{{ $stage }}' && 'is-over'"
                    @dragover.prevent="overStage = '{{ $stage }}'"
                    @dragleave="if (overStage === '{{ $stage }}') overStage = null"
                    @drop.prevent="dropOn('{{ $stage }}', $event)"
                >
                    <header class="admin-pipeline__head">
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="text-sm font-semibold capitalize">{{ str_replace('_', ' ', $stage) }}</h2>
                            <span class="admin-badge" x-text="count('{{ $stage }}')">{{ ($grouped[$stage] ?? collect())->count() }}</span>
                        </div>
                        <p class="mt-1 text-xs text-zinc-500" x-text="formatKes(totalValue('{{ $stage }}'))"></p>
                    </header>
                    <div class="admin-pipeline__list">
                        <template x-for="card in cards('{{ $stage }}')" :key="card.id">
                            <article
                                class="admin-pipeline__card"
                                :class="draggingId == card.id && 'is-dragging'"
                                draggable="true"
                                @dragstart="dragStart(card.id, $event)"
                                @dragend="dragEnd()"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <a class="admin-table__name text-sm" :href="card.url" x-text="card.name"></a>
                                    <span
                                        class="admin-badge capitalize"
                                        :class="{
                                            'admin-badge--danger': card.priority === 'urgent',
                                            'admin-badge--warn': card.priority === 'high',
                                            'admin-badge--navy': card.priority === 'normal',
                                        }"
                                        x-text="card.priority"
                                    ></span>
                                </div>
                                <p class="admin-table__meta" x-text="card.reference"></p>
                                <p class="mt-2 text-xs text-zinc-600" x-text="card.product || 'No product'"></p>
                                <p class="mt-1 text-sm font-semibold text-zinc-900" x-text="formatKes(card.value)"></p>
                                <div class="mt-2 flex items-center justify-between gap-2 text-xs text-zinc-500">
                                    <span x-text="card.assignee || 'Unassigned'"></span>
                                    <span class="text-[#d42127]" x-show="card.overdue">Overdue</span>
                                    <span x-show="!card.overdue && card.followUp" x-text="card.followUp"></span>
                                </div>
                                <label class="mt-3 block text-[11px] font-semibold text-zinc-500 lg:hidden">
                                    Move
                                    <select
                                        class="admin-input mt-1 !py-1.5 text-xs"
                                        :value="'{{ $stage }}'"
                                        @change="move(card.id, $event.target.value); $event.target.value = '{{ $stage }}'"
                                    >
                                        @foreach ($stages as $option)
                                            <option value="{{ $option }}">{{ str_replace('_', ' ', $option) }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            </article>
                        </template>
                        <p class="px-1 py-6 text-center text-xs text-zinc-400" x-show="count('{{ $stage }}') === 0">Drop a lead here</p>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
@endsection
