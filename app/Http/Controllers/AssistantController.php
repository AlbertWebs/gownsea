<?php

namespace App\Http\Controllers;

use App\Mail\AssistantSubmissionReceived;
use App\Models\AssistantRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AssistantController extends Controller
{
    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:40'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'website' => ['nullable', 'max:0'],
        ]);

        $assistantRequest = AssistantRequest::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
        ]);

        $recipient = config('gownsea.assistant.admin_email', config('mail.from.address'));

        if ($recipient) {
            Mail::to($recipient)->send(new AssistantSubmissionReceived($assistantRequest));
        }

        return back()->with('assistant_status', 'Your message has been sent. We will contact you shortly.');
    }
}
