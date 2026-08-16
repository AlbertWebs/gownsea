<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AuditLog;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\User;
use App\Services\InquiryCaptureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InquiryController extends Controller
{
    public function products(Request $request): View
    {
        return $this->index($request, 'product');
    }

    public function general(Request $request): View
    {
        return $this->index($request, 'general');
    }

    public function show(Request $request, Inquiry $inquiry): View
    {
        $this->authorizeView($request, $inquiry);
        $inquiry->load(['product', 'customer', 'assignee', 'lead', 'activities.user']);

        if (! $inquiry->is_read) {
            $inquiry->update(['is_read' => true]);
        }

        return view('admin.inquiries.show', [
            'inquiry' => $inquiry,
            'users' => User::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $this->authorizeView($request, $inquiry);
        $data = $request->validate([
            'status' => ['nullable', 'in:'.implode(',', config('admin.inquiry_statuses'))],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'follow_up_at' => ['nullable', 'date'],
        ]);

        if (array_key_exists('assigned_to', $data) && (int) $data['assigned_to'] !== (int) $inquiry->assigned_to) {
            $data['assigned_at'] = now();
            Activity::query()->create([
                'type' => 'note',
                'subject_type' => Inquiry::class,
                'subject_id' => $inquiry->id,
                'user_id' => $request->user()->id,
                'title' => 'Inquiry assigned',
                'status' => 'completed',
            ]);
        }

        $inquiry->update(array_filter($data, fn ($v) => $v !== null));

        return back()->with('status', 'Inquiry assigned successfully.');
    }

    public function note(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $this->authorizeView($request, $inquiry);
        $data = $request->validate(['description' => ['required', 'string', 'max:2000']]);

        Activity::query()->create([
            'type' => 'note',
            'subject_type' => Inquiry::class,
            'subject_id' => $inquiry->id,
            'customer_id' => $inquiry->customer_id,
            'user_id' => $request->user()->id,
            'title' => 'Internal note',
            'description' => $data['description'],
            'status' => 'completed',
        ]);

        return back()->with('status', 'Note added.');
    }

    public function convert(Request $request, Inquiry $inquiry, InquiryCaptureService $capture): RedirectResponse
    {
        $this->authorizeView($request, $inquiry);

        if ($inquiry->lead_id) {
            return redirect()->route('admin.leads.show', $inquiry->lead_id)->with('status', 'Lead already exists for this inquiry.');
        }

        $lead = Lead::query()->create([
            'reference' => 'LD-'.Str::upper(Str::random(6)),
            'customer_id' => $inquiry->customer_id,
            'product_id' => $inquiry->product_id,
            'inquiry_id' => $inquiry->id,
            'assigned_to' => $inquiry->assigned_to ?: $request->user()->id,
            'name' => $inquiry->name,
            'email' => $inquiry->email,
            'phone' => $inquiry->phone,
            'source' => $inquiry->source,
            'estimated_value' => $inquiry->product?->price_amount ?: 0,
            'probability' => 20,
            'stage' => 'new',
            'priority' => $inquiry->priority,
            'notes' => $inquiry->message,
        ]);

        $inquiry->update(['lead_id' => $lead->id, 'status' => 'converted']);

        Activity::query()->create([
            'type' => 'note',
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'lead_id' => $lead->id,
            'customer_id' => $inquiry->customer_id,
            'user_id' => $request->user()->id,
            'title' => 'Lead created from inquiry',
            'status' => 'completed',
        ]);

        return redirect()->route('admin.leads.show', $lead)->with('status', 'Lead created from inquiry.');
    }

    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filtered($request, $request->string('type', 'product')->toString())->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Name', 'Email', 'Phone', 'Type', 'Status', 'Created']);
            foreach ($rows as $row) {
                fputcsv($out, [$row->id, $row->name, $row->email, $row->phone, $row->type, $row->status, $row->created_at]);
            }
            fclose($out);
        }, 'inquiries.csv');
    }

    private function index(Request $request, string $type): View
    {
        $inquiries = $this->filtered($request, $type)->paginate(20)->withQueryString();

        return view('admin.inquiries.index', [
            'inquiries' => $inquiries,
            'type' => $type,
            'users' => User::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    private function filtered(Request $request, string $type)
    {
        $query = Inquiry::query()->with(['product', 'assignee'])->visibleTo($request->user())->latest();
        $query->where('type', $type === 'product' ? 'product' : 'general');

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        return $query
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('source'), fn ($q) => $q->where('source', $request->string('source')))
            ->when($request->filled('assigned_to'), fn ($q) => $q->where('assigned_to', $request->integer('assigned_to')));
    }

    private function authorizeView(Request $request, Inquiry $inquiry): void
    {
        if ($request->user()->seesOnlyAssigned() && (int) $inquiry->assigned_to !== (int) $request->user()->id) {
            abort(403);
        }
    }
}
