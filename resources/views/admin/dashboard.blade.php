@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1>Dashboard</h1>
            <p class="mt-1 text-sm text-zinc-600">Operational view of catalogue, inquiries, leads and sales.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(auth()->user()->hasPermission('catalogue'))<a class="btn-secondary" href="{{ route('admin.catalogue.products.create') }}">Add Product</a>@endif
            @if(auth()->user()->hasPermission('leads'))<a class="btn-secondary" href="{{ route('admin.leads.create') }}">Add Lead</a>@endif
            @if(auth()->user()->hasPermission('sales'))<a class="btn-secondary" href="{{ route('admin.sales.create') }}">Create Sale</a>@endif
            @if(auth()->user()->hasPermission('customers'))<a class="btn-primary" href="{{ route('admin.customers.create') }}">Add Customer</a>@endif
        </div>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([['Revenue', 'KES '.number_format($kpis['revenue'])], ['Sales', $kpis['sales']], ['Leads', $kpis['leads']], ['Inquiries', $kpis['inquiries']]] as [$label, $value])
            <article class="admin-card">
                <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-semibold">{{ $value }}</p>
            </article>
        @endforeach
    </div>
    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Active products', $kpis['active_products']],
            ['New inquiries ('.$range.'d)', $kpis['new_inquiries']],
            ['Qualified leads', $kpis['qualified_leads']],
            ['Conversion rate', $kpis['conversion'].'%'],
            ['Pending sales', $kpis['pending_sales']],
            ['Won leads', $kpis['won_leads']],
            ['New leads', $kpis['new_leads']],
            ['Overdue follow-ups', $overdue],
        ] as [$label, $value])
            <article class="admin-card">
                <p class="text-xs text-zinc-500">{{ $label }}</p>
                <p class="mt-1 text-xl font-semibold">{{ $value }}</p>
            </article>
        @endforeach
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <article class="admin-card lg:col-span-2">
            <h2>Sales overview</h2>
            <canvas id="salesChart" height="120"></canvas>
        </article>
        <article class="admin-card">
            <h2>Lead pipeline</h2>
            <ul class="mt-4 space-y-2 text-sm">
                @foreach ($pipeline as $stage => $count)
                    <li class="flex justify-between"><span>{{ str_replace('_', ' ', $stage) }}</span><strong>{{ $count }}</strong></li>
                @endforeach
            </ul>
        </article>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        <article class="admin-card">
            <h2>Recent leads</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @forelse ($recentLeads as $lead)
                    <li><a class="text-[#d42127]" href="{{ route('admin.leads.show', $lead) }}">{{ $lead->name }}</a> · {{ $lead->stage }}</li>
                @empty
                    <li class="text-zinc-500">No leads have been added yet. <a class="text-[#d42127]" href="{{ route('admin.leads.create') }}">Add Lead</a></li>
                @endforelse
            </ul>
        </article>
        <article class="admin-card">
            <h2>Recent inquiries</h2>
            <ul class="mt-4 space-y-3 text-sm">
                @forelse ($recentInquiries as $inquiry)
                    <li><a class="text-[#d42127]" href="{{ route('admin.inquiries.show', $inquiry) }}">{{ $inquiry->name }}</a> · {{ \Illuminate\Support\Str::limit($inquiry->message, 60) }}</li>
                @empty
                    <li class="text-zinc-500">No inquiries have been received yet.</li>
                @endforelse
            </ul>
        </article>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        <article class="admin-card">
            <h2>Top products</h2>
            <ul class="mt-4 space-y-2 text-sm">
                @foreach ($topProducts as $product)
                    <li class="flex justify-between"><a href="{{ route('admin.catalogue.products.show', $product) }}">{{ $product->name }}</a><span>{{ $product->inquiries_count }} inquiries</span></li>
                @endforeach
            </ul>
        </article>
        <article class="admin-card">
            <h2>Upcoming follow-ups</h2>
            <ul class="mt-4 space-y-2 text-sm">
                @forelse ($upcomingFollowUps as $activity)
                    <li>{{ $activity->title }} · {{ optional($activity->due_at)->format('d M Y H:i') }}</li>
                @empty
                    <li class="text-zinc-500">No follow-ups scheduled.</li>
                @endforelse
            </ul>
        </article>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('salesChart');
            if (!el || !window.Chart) return;
            const data = @json($salesSeries);
            new Chart(el, {
                type: 'line',
                data: { labels: Object.keys(data), datasets: [{ label: 'Revenue', data: Object.values(data), borderColor: '#d42127', backgroundColor: 'rgba(212,33,39,.08)', tension: .3 }] },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });
        });
    </script>
@endsection
