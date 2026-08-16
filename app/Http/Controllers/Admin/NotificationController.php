<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read(Request $request, AdminNotification $notification): RedirectResponse
    {
        $notification->update(['read_at' => now()]);

        return redirect($notification->url ?: route('admin.dashboard'));
    }

    public function readAll(Request $request): RedirectResponse
    {
        AdminNotification::query()->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('status', 'All notifications marked as read.');
    }
}
