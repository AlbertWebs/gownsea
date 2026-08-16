<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Services\InquiryCaptureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.sales.index', [
            'sales' => $this->filtered($request)->paginate(20)->withQueryString(),
            'users' => User::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.sales.form', [
            'sale' => new Sale(['status' => 'draft', 'payment_status' => 'unpaid']),
            'customers' => Customer::query()->orderBy('name')->get(),
            'products' => Product::query()->orderBy('name')->get(),
            'users' => User::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, InquiryCaptureService $capture): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'new_customer_name' => ['nullable', 'string', 'max:190'],
            'new_customer_email' => ['nullable', 'email'],
            'new_customer_phone' => ['nullable', 'string', 'max:40'],
            'salesperson_id' => ['nullable', 'exists:users,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'status' => ['required', 'in:'.implode(',', config('admin.sale_statuses'))],
            'payment_status' => ['required', 'in:'.implode(',', config('admin.payment_statuses'))],
            'discount' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'integer', 'min:0'],
        ]);

        $customerId = $data['customer_id'] ?? null;
        if (! $customerId && ($data['new_customer_name'] ?? null)) {
            $customerId = $capture->matchOrCreateCustomer(
                $data['new_customer_name'],
                $data['new_customer_email'] ?? null,
                $data['new_customer_phone'] ?? null
            )->id;
        }

        $sale = Sale::query()->create([
            'number' => 'SL-'.now()->format('ymd').'-'.Str::upper(Str::random(4)),
            'customer_id' => $customerId,
            'lead_id' => $data['lead_id'] ?? null,
            'salesperson_id' => $data['salesperson_id'] ?? $request->user()->id,
            'source' => 'manual',
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'discount' => $data['discount'] ?? 0,
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($data['items'] as $item) {
            $product = ! empty($item['product_id']) ? Product::query()->find($item['product_id']) : null;
            $qty = (int) $item['quantity'];
            $price = (int) $item['unit_price'];
            SaleItem::query()->create([
                'sale_id' => $sale->id,
                'product_id' => $product?->id,
                'name' => $product?->name ?? 'Custom item',
                'quantity' => $qty,
                'unit_price' => $price,
                'line_total' => $qty * $price,
            ]);
        }

        $sale->recalculate();

        return redirect()->route('admin.sales.show', $sale)->with('status', 'Sale created successfully.');
    }

    public function show(Request $request, Sale $sale): View
    {
        if ($request->user()->seesOnlyAssigned() && (int) $sale->salesperson_id !== (int) $request->user()->id) {
            abort(403);
        }

        $sale->load(['customer', 'salesperson', 'lead', 'items.product', 'activities.user']);

        return view('admin.sales.show', ['sale' => $sale]);
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', config('admin.sale_statuses'))],
            'payment_status' => ['required', 'in:'.implode(',', config('admin.payment_statuses'))],
            'notes' => ['nullable', 'string'],
        ]);
        $sale->update($data);

        return back()->with('status', 'Sale updated successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filtered($request)->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Number', 'Customer', 'Total', 'Status', 'Payment', 'Date']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->number, $row->customer?->name, $row->total, $row->status, $row->payment_status, $row->created_at]);
            }
            fclose($out);
        }, 'sales.csv');
    }

    private function filtered(Request $request)
    {
        $query = Sale::query()->with(['customer', 'salesperson'])->visibleTo($request->user())->latest();

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
            });
        }

        return $query
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->string('payment_status')))
            ->when($request->filled('salesperson_id'), fn ($q) => $q->where('salesperson_id', $request->integer('salesperson_id')));
    }
}
