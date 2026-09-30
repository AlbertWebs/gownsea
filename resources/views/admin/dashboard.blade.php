@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
    @php
        $range = (int) $range;
        $delta = fn (int $current, int $previous) => $previous > 0 ? round((($current - $previous) / $previous) * 100) : null;
        $visitorsDelta = $delta($kpis['visitors'], $previousVisitors);
        $viewsDelta = $delta($kpis['page_views'], $previousPageViews);
        $inquiriesDelta = $delta($kpis['new_inquiries'], $previousInquiries);
        $maxPipeline = max(1, (int) $pipeline->max());
        $maxSource = max(1, (int) $trafficSources->max('total'));
        $maxReferrer = max(1, (int) $referrers->max('total'));
        $maxPage = max(1, (int) $popularPages->max('total'));
        $sourceColors = ['#d42127', '#0f2744', '#0f9f8f', '#f59e0b', '#7c3aed', '#64748b'];
    @endphp

    <section class="admin-dashboard">
        <header class="admin-dashboard__header">
            <div>
                <p class="admin-eyebrow">Gownsea operations</p>
                <h1>Dashboard</h1>
                <p class="admin-dashboard__lede">A clear view of website traffic, enquiries, leads, and sales.</p>
            </div>
            <div class="admin-dashboard__actions">
                @if(auth()->user()->hasPermission('catalogue'))<x-admin.btn :href="route('admin.catalogue.products.create')" variant="ghost" icon="plus">Add product</x-admin.btn>@endif
                @if(auth()->user()->hasPermission('leads'))<x-admin.btn :href="route('admin.leads.create')" variant="ghost" icon="plus">Add lead</x-admin.btn>@endif
                @if(auth()->user()->hasPermission('sales'))<x-admin.btn :href="route('admin.sales.create')" variant="navy" icon="plus">Create sale</x-admin.btn>@endif
            </div>
        </header>

        <div class="admin-dashboard__toolbar">
            <div>
                <strong>Performance</strong>
                <span>Last {{ $range }} days</span>
            </div>
            <nav class="admin-range-tabs" aria-label="Dashboard date range">
                @foreach ([7 => '7 days', 30 => '30 days', 90 => '90 days'] as $value => $label)
                    <a href="{{ route('admin.dashboard', ['range' => $value]) }}" class="{{ $range === $value ? 'is-active' : '' }}" @if($range === $value) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>
        </div>

        <div class="admin-metric-grid">
            @foreach ([
                ['Unique visitors', number_format($kpis['visitors']), 'People who visited during this period', $visitorsDelta, 'visitor'],
                ['Page views', number_format($kpis['page_views']), 'Pages viewed during this period', $viewsDelta, 'views'],
                ['New inquiries', number_format($kpis['new_inquiries']), 'All website enquiries', $inquiriesDelta, 'inquiry'],
                ['New leads', number_format($kpis['new_leads']), 'Leads added in this period', null, 'lead'],
            ] as [$label, $value, $hint, $change, $icon])
                <article class="admin-metric-card">
                    <div class="admin-metric-card__top">
                        <span>{{ $label }}</span>
                        <span class="admin-metric-card__icon admin-metric-card__icon--{{ $icon }}" aria-hidden="true">
                            @if($icon === 'visitor')<svg viewBox="0 0 24 24" fill="none"><path d="M4 19c.8-3.2 3.5-5 8-5s7.2 1.8 8 5M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            @elseif($icon === 'views')<svg viewBox="0 0 24 24" fill="none"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg>
                            @elseif($icon === 'inquiry')<svg viewBox="0 0 24 24" fill="none"><path d="M4 5.5h16v12H9l-5 3v-15Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 10h8M8 13.5h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            @else<svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 20v-1.2c0-2.4 2-4.3 4.4-4.3h2.2c2.4 0 4.4 1.9 4.4 4.3V20M17 8h4m-2-2v4m-2 6c2.1.2 3.5 1.8 3.5 3.8v.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>@endif
                        </span>
                    </div>
                    <p class="admin-metric-card__value">{{ $value }}</p>
                    <div class="admin-metric-card__bottom">
                        <span>{{ $hint }}</span>
                        @if($change !== null)
                            <span class="admin-delta {{ $change >= 0 ? 'is-up' : 'is-down' }}">{{ $change >= 0 ? '+' : '' }}{{ $change }}% <span class="sr-only">versus previous period</span></span>
                        @elseif($icon === 'lead')
                            <a href="{{ route('admin.leads.index') }}">View leads <span aria-hidden="true">→</span></a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="admin-summary-strip">
            <div><span>Revenue</span><strong>KES {{ number_format($kpis['revenue']) }}</strong><small>{{ $kpis['sales'] }} total sales</small></div>
            <div><span>Qualified leads</span><strong>{{ number_format($kpis['qualified_leads']) }}</strong><small>Across all pipeline stages</small></div>
            <div><span>Won leads</span><strong>{{ number_format($kpis['won_leads']) }}</strong><small>{{ $kpis['conversion'] }}% lifetime win rate</small></div>
            <div><span>Inquiry rate</span><strong>{{ $kpis['inquiry_rate'] }}%</strong><small>Of unique visitors this period</small></div>
            <div><span>Active products</span><strong>{{ number_format($kpis['active_products']) }}</strong><small>Of {{ number_format($kpis['products']) }} catalogue items</small></div>
            <div><span>Follow-ups due</span><strong class="{{ $overdue ? 'text-[#d42127]' : '' }}">{{ number_format($overdue) }}</strong><small>{{ $kpis['pending_sales'] }} sales in progress</small></div>
        </div>

        <div class="admin-dashboard__grid admin-dashboard__grid--traffic">
            <article class="admin-card admin-chart-card admin-chart-card--large">
                <div class="admin-card-heading">
                    <div><p class="admin-eyebrow">Audience</p><h2>Traffic over time</h2><p>Unique visitors compared with total page views.</p></div>
                    <span class="admin-badge admin-badge--info">Last {{ $range }} days</span>
                </div>
                @if($kpis['page_views'] > 0)
                    <div class="admin-chart-wrap"><canvas id="trafficChart" aria-label="Daily website visitors and page views" role="img"></canvas></div>
                @else
                    <div class="admin-chart-empty"><span aria-hidden="true">↗</span><strong>Traffic data will appear here</strong><p>Page visits are counted from the moment analytics is enabled.</p></div>
                @endif
            </article>

            <article class="admin-card">
                <div class="admin-card-heading"><div><p class="admin-eyebrow">CRM</p><h2>Lead pipeline</h2><p>Current lead volume by stage.</p></div><a class="admin-text-link" href="{{ route('admin.leads.pipeline') }}">Open pipeline <span aria-hidden="true">→</span></a></div>
                <div class="admin-pipeline-list">
                    @foreach ($pipeline as $stage => $count)
                        <div class="admin-pipeline-row">
                            <div><span>{{ str_replace('_', ' ', $stage) }}</span><strong>{{ $count }}</strong></div>
                            <div class="admin-progress"><span style="width: {{ min(100, (int) round(($count / $maxPipeline) * 100)) }}%"></span></div>
                        </div>
                    @endforeach
                </div>
                <div class="admin-card-footnote"><span>Weighted forecast</span><strong>KES {{ number_format($weightedForecast) }}</strong></div>
            </article>
        </div>

        <div class="admin-dashboard__grid admin-dashboard__grid--sources">
            <article class="admin-card">
                <div class="admin-card-heading"><div><p class="admin-eyebrow">Acquisition</p><h2>Traffic sources</h2><p>Where visits started.</p></div></div>
                @forelse($trafficSources as $index => $source)
                    <div class="admin-ranked-row">
                        <span class="admin-ranked-row__dot" style="--rank-color: {{ $sourceColors[$index % count($sourceColors)] }}"></span>
                        <span class="admin-ranked-row__label">{{ $source->source ?: 'Other' }}</span>
                        <span class="admin-ranked-row__bar"><i style="width: {{ min(100, (int) round(($source->total / $maxSource) * 100)) }}%; --rank-color: {{ $sourceColors[$index % count($sourceColors)] }}"></i></span>
                        <strong>{{ number_format($source->total) }}</strong>
                    </div>
                @empty
                    <div class="admin-empty-note">Traffic source data will appear as people visit the site.</div>
                @endforelse
                <div class="admin-device-row">
                    @foreach (['mobile' => 'Mobile', 'desktop' => 'Desktop', 'tablet' => 'Tablet'] as $device => $label)
                        <span>{{ $label }} <strong>{{ number_format((int) ($deviceBreakdown[$device] ?? 0)) }}</strong></span>
                    @endforeach
                </div>
            </article>

            <article class="admin-card">
                <div class="admin-card-heading"><div><p class="admin-eyebrow">Referrals</p><h2>Top referrers</h2><p>External sites that sent visitors.</p></div></div>
                @forelse($referrers as $referrer)
                    <div class="admin-ranked-row">
                        <span class="admin-ranked-row__favicon" aria-hidden="true">{{ strtoupper(substr($referrer->referrer_host, 0, 1)) }}</span>
                        <span class="admin-ranked-row__label">{{ $referrer->referrer_host }}</span>
                        <span class="admin-ranked-row__bar"><i style="width: {{ min(100, (int) round(($referrer->total / $maxReferrer) * 100)) }}%; --rank-color: #0f2744"></i></span>
                        <strong>{{ number_format($referrer->total) }}</strong>
                    </div>
                @empty
                    <div class="admin-empty-note">External referrers will appear here. Direct visits are not included.</div>
                @endforelse
            </article>

            <article class="admin-card">
                <div class="admin-card-heading"><div><p class="admin-eyebrow">Content</p><h2>Popular pages</h2><p>Most viewed pages in this period.</p></div></div>
                @forelse($popularPages as $page)
                    <div class="admin-ranked-row">
                        <span class="admin-ranked-row__favicon" aria-hidden="true">↗</span>
                        <span class="admin-ranked-row__label admin-ranked-row__label--path" title="{{ $page->path }}">{{ $page->path }}</span>
                        <span class="admin-ranked-row__bar"><i style="width: {{ min(100, (int) round(($page->total / $maxPage) * 100)) }}%; --rank-color: #d42127"></i></span>
                        <strong>{{ number_format($page->total) }}</strong>
                    </div>
                @empty
                    <div class="admin-empty-note">Popular pages will appear after the first visits are recorded.</div>
                @endforelse
            </article>
        </div>

        <div class="admin-dashboard__grid admin-dashboard__grid--crm">
            <article class="admin-card admin-chart-card">
                <div class="admin-card-heading"><div><p class="admin-eyebrow">Conversion</p><h2>Enquiries and leads</h2><p>New activity each day.</p></div></div>
                <div class="admin-chart-wrap admin-chart-wrap--small"><canvas id="crmChart" aria-label="Daily inquiries and leads" role="img"></canvas></div>
            </article>
            <article class="admin-card">
                <div class="admin-card-heading"><div><p class="admin-eyebrow">Lead quality</p><h2>Lead sources</h2><p>How recent leads found Gownsea.</p></div></div>
                @forelse($leadSources as $source => $total)
                    <div class="admin-simple-list-row"><span>{{ $source ?: 'Not specified' }}</span><strong>{{ number_format($total) }}</strong></div>
                @empty
                    <div class="admin-empty-note">New lead sources will be listed here.</div>
                @endforelse
            </article>
            <article class="admin-card admin-chart-card">
                <div class="admin-card-heading"><div><p class="admin-eyebrow">Sales</p><h2>Revenue trend</h2><p>Recorded sales by day.</p></div></div>
                <div class="admin-chart-wrap admin-chart-wrap--small"><canvas id="revenueChart" aria-label="Daily sales revenue" role="img"></canvas></div>
            </article>
        </div>

        <div class="admin-dashboard__grid admin-dashboard__grid--lists">
            <article class="admin-card admin-list-card">
                <div class="admin-card-heading"><div><p class="admin-eyebrow">Follow through</p><h2>Recent leads</h2><p>Latest people in your pipeline.</p></div><a class="admin-text-link" href="{{ route('admin.leads.index') }}">All leads <span aria-hidden="true">→</span></a></div>
                @forelse($recentLeads as $lead)
                    <a class="admin-contact-row" href="{{ route('admin.leads.show', $lead) }}">
                        <span class="admin-contact-avatar">{{ strtoupper(substr($lead->name, 0, 1)) }}</span>
                        <span class="admin-contact-copy"><strong>{{ $lead->name }}</strong><small>{{ $lead->company ?: $lead->source ?: 'Lead' }}</small></span>
                        <span class="admin-badge admin-badge--navy">{{ str_replace('_', ' ', $lead->stage) }}</span>
                    </a>
                @empty
                    <div class="admin-empty-note">No leads yet. New enquiries can be converted into leads.</div>
                @endforelse
            </article>
            <article class="admin-card admin-list-card">
                <div class="admin-card-heading"><div><p class="admin-eyebrow">Inbox</p><h2>Recent inquiries</h2><p>Latest customer requests.</p></div><a class="admin-text-link" href="{{ route('admin.inquiries.products') }}">Open inbox <span aria-hidden="true">→</span></a></div>
                @forelse($recentInquiries as $inquiry)
                    <a class="admin-contact-row" href="{{ route('admin.inquiries.show', $inquiry) }}">
                        <span class="admin-contact-avatar admin-contact-avatar--red">{{ strtoupper(substr($inquiry->name, 0, 1)) }}</span>
                        <span class="admin-contact-copy"><strong>{{ $inquiry->name }}</strong><small>{{ \Illuminate\Support\Str::limit($inquiry->message, 55) }}</small></span>
                        <span class="admin-contact-date">{{ $inquiry->created_at->diffForHumans() }}</span>
                    </a>
                @empty
                    <div class="admin-empty-note">No enquiries received yet.</div>
                @endforelse
            </article>
            <article class="admin-card admin-list-card">
                <div class="admin-card-heading"><div><p class="admin-eyebrow">Catalogue</p><h2>Popular products</h2><p>Products attracting the most enquiries.</p></div><a class="admin-text-link" href="{{ route('admin.catalogue.products.index') }}">Products <span aria-hidden="true">→</span></a></div>
                @forelse($topProducts as $product)
                    <a class="admin-simple-list-row" href="{{ route('admin.catalogue.products.show', $product) }}"><span>{{ $product->name }}</span><strong>{{ $product->inquiries_count }} enquiries</strong></a>
                @empty
                    <div class="admin-empty-note">Products with enquiries will appear here.</div>
                @endforelse
            </article>
            <article class="admin-card admin-list-card">
                <div class="admin-card-heading"><div><p class="admin-eyebrow">Stay on track</p><h2>Upcoming follow-ups</h2><p>Next scheduled customer actions.</p></div><a class="admin-text-link" href="{{ route('admin.activities.index') }}">Activities <span aria-hidden="true">→</span></a></div>
                @forelse($upcomingFollowUps as $activity)
                    <div class="admin-followup-row"><span class="admin-followup-row__date">{{ optional($activity->due_at)->format('d M') }}</span><span><strong>{{ $activity->title }}</strong><small>{{ $activity->lead?->name ?? 'General follow-up' }}</small></span></div>
                @empty
                    <div class="admin-empty-note">No follow-ups scheduled.</div>
                @endforelse
            </article>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.Chart) return;
            const chart = @json($chart);
            const colors = { red: '#d42127', navy: '#0f2744', teal: '#0f9f8f', amber: '#e9a323' };
            const defaults = { responsive: true, maintainAspectRatio: false, interaction: { intersect: false, mode: 'index' }, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 7, padding: 18 } } }, scales: { x: { grid: { display: false }, ticks: { color: '#8892a0', maxTicksLimit: 8 } }, y: { beginAtZero: true, border: { display: false }, grid: { color: '#eef1f5' }, ticks: { color: '#8892a0', precision: 0 } } } };
            const line = (canvas, datasets, options = {}) => {
                const element = document.getElementById(canvas);
                if (!element) return;
                new Chart(element, { type: 'line', data: { labels: chart.labels, datasets }, options: { ...defaults, ...options } });
            };
            line('trafficChart', [
                { label: 'Unique visitors', data: chart.visitors, borderColor: colors.navy, backgroundColor: 'rgba(15,39,68,.08)', fill: true, tension: .36, pointRadius: 2, borderWidth: 2 },
                { label: 'Page views', data: chart.pageViews, borderColor: colors.red, backgroundColor: 'transparent', tension: .36, pointRadius: 1, borderWidth: 2 },
            ]);
            line('crmChart', [
                { label: 'Inquiries', data: chart.inquiries, borderColor: colors.teal, backgroundColor: 'rgba(15,159,143,.09)', fill: true, tension: .36, pointRadius: 2, borderWidth: 2 },
                { label: 'Leads', data: chart.leads, borderColor: colors.amber, backgroundColor: 'transparent', tension: .36, pointRadius: 2, borderWidth: 2 },
            ]);
            line('revenueChart', [{ label: 'Revenue (KES)', data: chart.revenue, borderColor: colors.red, backgroundColor: 'rgba(212,33,39,.08)', fill: true, tension: .36, pointRadius: 2, borderWidth: 2 }], { plugins: { legend: { display: false } } });
        });
    </script>
@endsection
