<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Services\InquiryCaptureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $query = $this->filtered($request);

        return view('admin.leads.index', [
            'leads' => $query->paginate(20)->withQueryString(),
            'users' => User::query()->where('status', 'active')->orderBy('name')->get(),
            'products' => Product::query()->orderBy('name')->get(),
            'duplicates' => [],
        ]);
    }

    public function pipeline(Request $request): View
    {
        $request->query->remove('stage');
        $leads = $this->filtered($request)->get();
        $open = $leads->whereNotIn('stage', ['won', 'lost']);

        return view('admin.leads.pipeline', [
            'leads' => $leads,
            'grouped' => $leads->groupBy('stage'),
            'stages' => config('admin.lead_stages'),
            'users' => User::query()->where('status', 'active')->orderBy('name')->get(),
            'stats' => [
                'total' => $leads->count(),
                'open' => $open->count(),
                'value' => (int) $open->sum('estimated_value'),
                'weighted' => (int) $open->sum(fn (Lead $lead) => $lead->weightedForecast()),
                'won' => $leads->where('stage', 'won')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.leads.form', [
            'lead' => new Lead(['stage' => 'new', 'priority' => 'normal', 'probability' => 10, 'source' => 'manual']),
            'products' => Product::query()->orderBy('name')->get(),
            'users' => User::query()->where('status', 'active')->orderBy('name')->get(),
            'warnings' => [],
        ]);
    }

    public function edit(Lead $lead): View
    {
        return view('admin.leads.form', [
            'lead' => $lead,
            'products' => Product::query()->orderBy('name')->get(),
            'users' => User::query()->where('status', 'active')->orderBy('name')->get(),
            'warnings' => [],
        ]);
    }

    public function store(Request $request, InquiryCaptureService $capture): RedirectResponse
    {
        $data = $this->validated($request);
        $warnings = $this->duplicateWarnings($data['email'] ?? null, $data['phone'] ?? null);

        if ($warnings && ! $request->boolean('confirm_duplicate')) {
            return back()->withInput()->with('warnings', $warnings);
        }

        $customer = $capture->matchOrCreateCustomer($data['name'], $data['email'] ?? null, $data['phone'] ?? null);
        $data['customer_id'] = $customer->id;
        $data['reference'] = 'LD-'.Str::upper(Str::random(6));
        $lead = Lead::query()->create($data);

        Activity::query()->create([
            'type' => 'note',
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'lead_id' => $lead->id,
            'customer_id' => $customer->id,
            'user_id' => $request->user()->id,
            'title' => 'Lead created',
            'status' => 'completed',
        ]);

        return redirect()->route('admin.leads.show', $lead)->with('status', 'Lead created successfully.');
    }

    public function show(Request $request, Lead $lead): View
    {
        $this->authorizeView($request, $lead);
        $lead->load(['customer', 'product', 'assignee', 'inquiry', 'sales', 'activities.user']);

        return view('admin.leads.show', [
            'lead' => $lead,
            'users' => User::query()->where('status', 'active')->orderBy('name')->get(),
            'stages' => config('admin.lead_stages'),
        ]);
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorizeView($request, $lead);
        $oldStage = $lead->stage;
        $oldAssignee = $lead->assigned_to;
        $lead->update($this->validated($request, $lead));

        if ($oldStage !== $lead->stage) {
            Activity::query()->create([
                'type' => 'note',
                'subject_type' => Lead::class,
                'subject_id' => $lead->id,
                'lead_id' => $lead->id,
                'user_id' => $request->user()->id,
                'title' => 'Stage changed to '.str_replace('_', ' ', $lead->stage),
                'status' => 'completed',
                'meta' => ['from' => $oldStage, 'to' => $lead->stage],
            ]);
            AuditLog::record($request->user(), 'lead.stage_changed', $lead, ['stage' => $oldStage], ['stage' => $lead->stage]);
        }

        if ((int) $oldAssignee !== (int) $lead->assigned_to) {
            Activity::query()->create([
                'type' => 'note',
                'subject_type' => Lead::class,
                'subject_id' => $lead->id,
                'lead_id' => $lead->id,
                'user_id' => $request->user()->id,
                'title' => 'Lead reassigned',
                'status' => 'completed',
            ]);
        }

        return back()->with('status', 'Lead moved to '.str_replace('_', ' ', $lead->stage).'.');
    }

    public function move(Request $request, Lead $lead): JsonResponse|RedirectResponse
    {
        $this->authorizeView($request, $lead);
        $data = $request->validate(['stage' => ['required', 'in:'.implode(',', config('admin.lead_stages'))]]);
        $from = $lead->stage;
        $lead->update([
            'stage' => $data['stage'],
            'won_at' => $data['stage'] === 'won' ? now() : $lead->won_at,
            'lost_at' => $data['stage'] === 'lost' ? now() : $lead->lost_at,
        ]);
        Activity::query()->create([
            'type' => 'note',
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'lead_id' => $lead->id,
            'user_id' => $request->user()->id,
            'title' => 'Stage changed to '.str_replace('_', ' ', $lead->stage),
            'status' => 'completed',
            'meta' => ['from' => $from, 'to' => $lead->stage],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('status', 'Lead moved to Qualified.');
    }

    public function convertSale(Request $request, Lead $lead): RedirectResponse
    {
        $this->authorizeView($request, $lead);
        $sale = Sale::query()->create([
            'number' => 'SL-'.now()->format('ymd').'-'.Str::upper(Str::random(4)),
            'customer_id' => $lead->customer_id,
            'lead_id' => $lead->id,
            'salesperson_id' => $lead->assigned_to ?: $request->user()->id,
            'source' => $lead->source,
            'status' => 'quotation',
            'payment_status' => 'unpaid',
            'notes' => $lead->notes,
        ]);

        if ($lead->product) {
            SaleItem::query()->create([
                'sale_id' => $sale->id,
                'product_id' => $lead->product_id,
                'name' => $lead->product->name,
                'quantity' => 1,
                'unit_price' => $lead->product->price_amount ?: $lead->estimated_value,
                'line_total' => $lead->product->price_amount ?: $lead->estimated_value,
            ]);
            $sale->recalculate();
        }

        $lead->update(['stage' => 'won', 'won_at' => now()]);

        return redirect()->route('admin.sales.show', $sale)->with('status', 'Sale created successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filtered($request)->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Reference', 'Name', 'Email', 'Phone', 'Stage', 'Value', 'Created']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->reference, $row->name, $row->email, $row->phone, $row->stage, $row->estimated_value, $row->created_at]);
            }
            fclose($out);
        }, 'leads.csv');
    }

    private function filtered(Request $request)
    {
        $query = Lead::query()->with(['product', 'assignee'])->visibleTo($request->user())->latest();

        if ($request->string('scope') === 'mine') {
            $query->where('assigned_to', $request->user()->id);
        }
        if ($request->string('scope') === 'unassigned') {
            $query->whereNull('assigned_to');
        }

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        return $query
            ->when($request->filled('stage'), fn ($q) => $q->where('stage', $request->string('stage')))
            ->when($request->filled('source'), fn ($q) => $q->where('source', $request->string('source')))
            ->when($request->filled('assigned_to'), fn ($q) => $q->where('assigned_to', $request->integer('assigned_to')))
            ->when($request->filled('product_id'), fn ($q) => $q->where('product_id', $request->integer('product_id')));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Lead $lead = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'company' => ['nullable', 'string', 'max:190'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'source' => ['required', 'in:'.implode(',', config('admin.sources'))],
            'product_id' => ['nullable', 'exists:products,id'],
            'estimated_value' => ['nullable', 'integer', 'min:0'],
            'probability' => ['nullable', 'integer', 'min:0', 'max:100'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'stage' => ['nullable', 'in:'.implode(',', config('admin.lead_stages'))],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'next_follow_up_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['estimated_value'] = (int) ($data['estimated_value'] ?? 0);
        $data['probability'] = (int) ($data['probability'] ?? ($lead?->probability ?? 10));
        $data['priority'] = $data['priority'] ?? ($lead?->priority ?? 'normal');
        $data['stage'] = $data['stage'] ?? ($lead?->stage ?? 'new');

        return $data;
    }

    /**
     * @return list<string>
     */
    private function duplicateWarnings(?string $email, ?string $phone): array
    {
        $warnings = [];
        if ($email && (Customer::query()->where('email', $email)->exists() || Lead::query()->where('email', $email)->whereNotIn('stage', ['won', 'lost'])->exists())) {
            $warnings[] = 'An existing customer or open lead already uses this email.';
        }
        if ($phone && Lead::query()->where('phone', $phone)->whereNotIn('stage', ['won', 'lost'])->exists()) {
            $warnings[] = 'An open lead already uses this phone number.';
        }

        return $warnings;
    }

    private function authorizeView(Request $request, Lead $lead): void
    {
        if ($request->user()->seesOnlyAssigned() && (int) $lead->assigned_to !== (int) $request->user()->id) {
            abort(403);
        }
    }
}
