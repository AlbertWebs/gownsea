<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $salesByProduct = Sale::query()
            ->with('items.product')
            ->where('status', 'completed')
            ->get()
            ->flatMap->items
            ->groupBy('product_id')
            ->map(fn ($items) => [
                'name' => $items->first()->name,
                'qty' => $items->sum('quantity'),
                'revenue' => $items->sum('line_total'),
            ])
            ->sortByDesc('revenue')
            ->take(10)
            ->values();

        return view('admin.reports.index', [
            'salesByDate' => Sale::query()->where('created_at', '>=', now()->subDays(30))->get()->groupBy(fn ($s) => $s->created_at->toDateString())->map->sum('total'),
            'salesByProduct' => $salesByProduct,
            'leadsBySource' => Lead::query()->selectRaw('source, count(*) as total')->groupBy('source')->pluck('total', 'source'),
            'leadsByStage' => Lead::query()->selectRaw('stage, count(*) as total')->groupBy('stage')->pluck('total', 'stage'),
            'won' => Lead::query()->where('stage', 'won')->count(),
            'lost' => Lead::query()->where('stage', 'lost')->count(),
            'inquiryVolume' => Inquiry::query()->where('created_at', '>=', now()->subDays(30))->count(),
            'mostEnquired' => Product::query()->withCount('inquiries')->orderByDesc('inquiries_count')->limit(8)->get(),
            'noInquiries' => Product::query()->doesntHave('inquiries')->limit(8)->get(),
            'highInquiryLowSales' => Product::query()
                ->whereHas('inquiries')
                ->withCount(['inquiries', 'saleItems'])
                ->orderBy('sale_items_count')
                ->limit(8)
                ->get(),
            'pipelineValue' => (int) Lead::query()->whereNotIn('stage', ['won', 'lost'])->sum('estimated_value'),
            'weightedPipeline' => (int) Lead::query()->whereNotIn('stage', ['won', 'lost'])->get()->sum(fn ($lead) => $lead->weightedForecast()),
        ]);
    }
}
