<?php

namespace App\Http\Controllers;

use App\Mail\AssistantSubmissionReceived;
use App\Services\InquiryCaptureService;
use App\Support\InquiryFormGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AssistantController extends Controller
{
    public function submit(Request $request, InquiryCaptureService $capture): RedirectResponse|JsonResponse
    {
        $validated = InquiryFormGuard::validate($request);

        $assistantRequest = $capture->capture($validated, $request);

        $recipient = config('gownsea.assistant.admin_email', config('mail.from.address'));

        if ($recipient) {
            Mail::to($recipient)->send(new AssistantSubmissionReceived($assistantRequest));
        }

        $status = 'Your message has been sent. We will contact you shortly.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => $status]);
        }

        return back()->with('assistant_status', $status);
    }
}
