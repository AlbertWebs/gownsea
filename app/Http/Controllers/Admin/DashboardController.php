<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $range = $request->string('range', '30')->toString();
        $days = in_array($range, ['7', '30', '90'], true) ? (int) $range : 30;
        $from = now()->subDays($days);
        $previousFrom = now()->subDays($days * 2);
        $previousTo = $from->copy();

        $kpis = [
            'products' => Product::query()->count(),
            'active_products' => Product::query()->published()->count(),
            'inquiries' => Inquiry::query()->count(),
            'new_inquiries' => Inquiry::query()->where('created_at', '>=', $from)->count(),
            'leads' => Lead::query()->count(),
            'new_leads' => Lead::query()->where('created_at', '>=', $from)->count(),
            'qualified_leads' => Lead::query()->where('stage', 'qualified')->count(),
            'won_leads' => Lead::query()->where('stage', 'won')->count(),
            'sales' => Sale::query()->count(),
            'revenue' => (int) Sale::query()->where('status', 'completed')->sum('total'),
            'pending_sales' => Sale::query()->whereIn('status', ['pending', 'confirmed', 'processing'])->count(),
        ];

        $won = $kpis['won_leads'];
        $leads = max(1, $kpis['leads']);
        $kpis['conversion'] = round(($won / $leads) * 100, 1);

        $pipeline = collect(config('admin.lead_stages'))->mapWithKeys(fn ($stage) => [
            $stage => Lead::query()->where('stage', $stage)->count(),
        ]);

        $inquiryOverview = collect(config('admin.inquiry_statuses'))->mapWithKeys(fn ($status) => [
            $status => Inquiry::query()->where('status', $status)->count(),
        ]);

        $leadSources = Lead::query()
            ->selectRaw('source, count(*) as total')
            ->groupBy('source')
            ->pluck('total', 'source');

        $salesSeries = Sale::query()
            ->where('created_at', '>=', $from)
            ->get()
            ->groupBy(fn (Sale $sale) => $sale->created_at->format('Y-m-d'))
            ->map(fn ($group) => (int) $group->sum('total'));

        $overdue = Activity::query()
            ->where('status', 'pending')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->count();

        return view('admin.dashboard', [
            'kpis' => $kpis,
            'range' => (string) $days,
            'pipeline' => $pipeline,
            'inquiryOverview' => $inquiryOverview,
            'leadSources' => $leadSources,
            'salesSeries' => $salesSeries,
            'recentLeads' => Lead::query()->latest()->limit(6)->get(),
            'recentInquiries' => Inquiry::query()->latest()->limit(6)->get(),
            'topProducts' => Product::query()->withCount('inquiries')->orderByDesc('inquiries_count')->limit(5)->get(),
            'upcomingFollowUps' => Activity::query()->where('status', 'pending')->whereNotNull('due_at')->orderBy('due_at')->limit(6)->get(),
            'overdue' => $overdue,
            'previousInquiries' => Inquiry::query()->whereBetween('created_at', [$previousFrom, $previousTo])->count(),
        ]);
    }
}
