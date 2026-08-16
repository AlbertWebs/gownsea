<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $query = Activity::query()->with(['lead', 'customer', 'user'])->latest('due_at')->latest();

        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')));

        if ($request->string('filter') === 'overdue') {
            $query->where('status', 'pending')->where('due_at', '<', now());
        }

        return view('admin.activities.index', [
            'activities' => $query->paginate(20)->withQueryString(),
            'leads' => Lead::query()->orderBy('name')->limit(100)->get(),
            'users' => User::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:'.implode(',', config('admin.activity_types'))],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'description' => ['required', 'string'],
            'due_at' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,completed,cancelled'],
        ]);

        $lead = ! empty($data['lead_id']) ? Lead::query()->find($data['lead_id']) : null;

        Activity::query()->create([
            ...$data,
            'user_id' => $request->user()->id,
            'customer_id' => $data['customer_id'] ?? $lead?->customer_id,
            'subject_type' => $lead ? Lead::class : null,
            'subject_id' => $lead?->id,
            'title' => Str::title(str_replace('_', ' ', $data['type'])),
        ]);

        return back()->with('status', 'Activity saved.');
    }
}
