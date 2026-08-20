<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MpesaController extends Controller
{
    public function stkPush(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'amount' => ['required', 'numeric', 'min:1'],
            'description' => ['required', 'string', 'max:255'],
            'product_slug' => ['required', 'string', 'max:120'],
        ]);

        $baseUrl = rtrim((string) config('services.kopokopo.base_url'), '/');
        $clientId = (string) config('services.kopokopo.client_id');
        $clientSecret = (string) config('services.kopokopo.client_secret');
        $tillNumber = (string) config('services.kopokopo.till_number');
        $callbackUrl = (string) config('services.kopokopo.callback_url');

        if (! $baseUrl || ! $clientId || ! $clientSecret || ! $tillNumber || ! $callbackUrl) {
            return response()->json([
                'message' => 'M-PESA is temporarily unavailable. Missing KopoKopo configuration.',
            ], 503);
        }

        $phone = preg_replace('/\D+/', '', $validated['phone']) ?? '';
        if (Str::startsWith($phone, '0')) {
            $phone = '254'.substr($phone, 1);
        } elseif (Str::startsWith($phone, '7')) {
            $phone = '254'.$phone;
        }

        try {
            $tokenResponse = Http::asForm()->post($baseUrl.'/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]);

            if (! $tokenResponse->successful()) {
                return response()->json([
                    'message' => 'Unable to authorize M-PESA request. Please try again.',
                ], 502);
            }

            $accessToken = (string) $tokenResponse->json('access_token');

            $stkResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->post($baseUrl.'/api/v1/incoming_payments/request', [
                    'payment_channel' => 'M-PESA STK Push',
                    'till_number' => $tillNumber,
                    'first_name' => 'Gownsea',
                    'last_name' => 'Customer',
                    'phone_number' => $phone,
                    'email' => 'payments@gownsea.com',
                    'currency' => 'KES',
                    'amount' => (float) $validated['amount'],
                    'description' => $validated['description'],
                    'callback_url' => $callbackUrl,
                    'metadata' => [
                        'product_slug' => $validated['product_slug'],
                    ],
                ]);

            if (! $stkResponse->successful()) {
                return response()->json([
                    'message' => 'STK push request failed. Confirm number and try again.',
                    'errors' => $stkResponse->json(),
                ], 502);
            }

            return response()->json([
                'message' => 'STK prompt sent successfully. Check your phone to complete payment.',
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'message' => 'Unable to process M-PESA request at the moment.',
            ], 500);
        }
    }
}
