<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SiteVisit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $range = $request->string('range', '30')->toString();
        $days = in_array($range, ['7', '30', '90'], true) ? (int) $range : 30;
        $today = now()->startOfDay();
        $from = $today->copy()->subDays($days - 1);
        $to = now()->endOfDay();
        $previousFrom = $from->copy()->subDays($days);
        $previousTo = $from->copy()->subSecond();

        $visits = SiteVisit::query()->whereBetween('created_at', [$from, $to]);
        $uniqueVisitors = (clone $visits)->distinct('visitor_hash')->count('visitor_hash');
        $pageViews = (clone $visits)->count();
        $periodInquiries = Inquiry::query()->whereBetween('created_at', [$from, $to])->count();
        $periodLeads = Lead::query()->whereBetween('created_at', [$from, $to])->count();

        $kpis = [
            'products' => Product::query()->count(),
            'active_products' => Product::query()->published()->count(),
            'inquiries' => Inquiry::query()->count(),
            'new_inquiries' => $periodInquiries,
            'leads' => Lead::query()->count(),
            'new_leads' => $periodLeads,
            'qualified_leads' => Lead::query()->where('stage', 'qualified')->count(),
            'won_leads' => Lead::query()->where('stage', 'won')->count(),
            'sales' => Sale::query()->count(),
            'revenue' => (int) Sale::query()->where('status', 'completed')->sum('total'),
            'pending_sales' => Sale::query()->whereIn('status', ['pending', 'confirmed', 'processing'])->count(),
        ];

        $won = $kpis['won_leads'];
        $leads = max(1, $kpis['leads']);
        $kpis['conversion'] = round(($won / $leads) * 100, 1);
        $kpis['visitors'] = $uniqueVisitors;
        $kpis['page_views'] = $pageViews;
        $kpis['inquiry_rate'] = $uniqueVisitors > 0 ? round(($periodInquiries / $uniqueVisitors) * 100, 1) : 0;

        $pipeline = collect(config('admin.lead_stages'))->mapWithKeys(fn ($stage) => [
            $stage => Lead::query()->where('stage', $stage)->count(),
        ]);
        $weightedForecast = (int) Lead::query()
            ->whereNotIn('stage', ['won', 'lost'])
            ->get()
            ->sum(fn (Lead $lead) => $lead->weightedForecast());

        $inquiryOverview = collect(config('admin.inquiry_statuses'))->mapWithKeys(fn ($status) => [
            $status => Inquiry::query()->where('status', $status)->count(),
        ]);

        $leadSources = Lead::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('source, count(*) as total')
            ->groupBy('source')
            ->pluck('total', 'source');

        $visitDaily = (clone $visits)
            ->selectRaw('DATE(created_at) as day, count(*) as views, count(distinct visitor_hash) as visitors')
            ->groupBy('day')
            ->get()
            ->keyBy('day');
        $inquiryDaily = Inquiry::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as day, count(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');
        $leadDaily = Lead::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as day, count(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');
        $salesDaily = Sale::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as day, sum(total) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $chart = ['labels' => [], 'visitors' => [], 'pageViews' => [], 'inquiries' => [], 'leads' => [], 'revenue' => []];
        for ($offset = $days - 1; $offset >= 0; $offset--) {
            $day = $today->copy()->subDays($offset);
            $key = $day->toDateString();
            $dayVisits = $visitDaily->get($key);
            $chart['labels'][] = $day->format($days <= 7 ? 'D' : 'M j');
            $chart['visitors'][] = (int) ($dayVisits->visitors ?? 0);
            $chart['pageViews'][] = (int) ($dayVisits->views ?? 0);
            $chart['inquiries'][] = (int) ($inquiryDaily[$key] ?? 0);
            $chart['leads'][] = (int) ($leadDaily[$key] ?? 0);
            $chart['revenue'][] = (int) ($salesDaily[$key] ?? 0);
        }

        $trafficSources = (clone $visits)
            ->selectRaw('source, count(*) as total')
            ->groupBy('source')
            ->orderByDesc('total')
            ->limit(6)
            ->get();
        $referrers = (clone $visits)
            ->whereNotNull('referrer_host')
            ->selectRaw('referrer_host, count(*) as total')
            ->groupBy('referrer_host')
            ->orderByDesc('total')
            ->limit(6)
            ->get();
        $popularPages = (clone $visits)
            ->selectRaw('path, count(*) as total')
            ->groupBy('path')
            ->orderByDesc('total')
            ->limit(6)
            ->get();
        $deviceBreakdown = (clone $visits)
            ->selectRaw('device, count(*) as total')
            ->groupBy('device')
            ->pluck('total', 'device');
        $previousVisitors = SiteVisit::query()
            ->whereBetween('created_at', [$previousFrom, $previousTo])
            ->distinct('visitor_hash')
            ->count('visitor_hash');
        $previousPageViews = SiteVisit::query()->whereBetween('created_at', [$previousFrom, $previousTo])->count();
        $previousInquiries = Inquiry::query()->whereBetween('created_at', [$previousFrom, $previousTo])->count();

        $overdue = Activity::query()
            ->where('status', 'pending')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->count();

        return view('admin.dashboard', [
            'kpis' => $kpis,
            'range' => (string) $days,
            'pipeline' => $pipeline,
            'weightedForecast' => $weightedForecast,
            'inquiryOverview' => $inquiryOverview,
            'leadSources' => $leadSources,
            'chart' => $chart,
            'trafficSources' => $trafficSources,
            'referrers' => $referrers,
            'popularPages' => $popularPages,
            'deviceBreakdown' => $deviceBreakdown,
            'previousVisitors' => $previousVisitors,
            'previousPageViews' => $previousPageViews,
            'previousInquiries' => $previousInquiries,
            'recentLeads' => Lead::query()->latest()->limit(6)->get(),
            'recentInquiries' => Inquiry::query()->latest()->limit(6)->get(),
            'topProducts' => Product::query()->withCount('inquiries')->orderByDesc('inquiries_count')->limit(5)->get(),
            'upcomingFollowUps' => Activity::query()->where('status', 'pending')->whereNotNull('due_at')->orderBy('due_at')->limit(6)->get(),
            'overdue' => $overdue,
        ]);
    }
}
