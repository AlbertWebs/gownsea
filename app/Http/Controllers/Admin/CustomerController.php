<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query()->withCount(['inquiries', 'leads', 'sales'])->latest();

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        return view('admin.customers.index', [
            'customers' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.customers.form', ['customer' => new Customer(['status' => 'active'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $customer = Customer::query()->create($this->validated($request));

        return redirect()->route('admin.customers.show', $customer)->with('status', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['inquiries' => fn ($q) => $q->latest()->limit(20), 'leads' => fn ($q) => $q->latest()->limit(20), 'sales' => fn ($q) => $q->latest()->limit(20), 'activities' => fn ($q) => $q->latest()->limit(20)]);

        return view('admin.customers.show', [
            'customer' => $customer,
            'spend' => (int) $customer->sales()->where('payment_status', 'paid')->sum('total'),
        ]);
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.form', ['customer' => $customer]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($this->validated($request));

        return back()->with('status', 'Customer updated successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = Customer::query()->withCount(['inquiries', 'leads', 'sales'])->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Name', 'Email', 'Phone', 'Inquiries', 'Leads', 'Sales']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->name, $row->email, $row->phone, $row->inquiries_count, $row->leads_count, $row->sales_count]);
            }
            fclose($out);
        }, 'customers.csv');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'company' => ['nullable', 'string', 'max:190'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
