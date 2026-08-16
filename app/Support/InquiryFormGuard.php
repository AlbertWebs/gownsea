<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InquiryFormGuard
{
    public static function token(): string
    {
        return encrypt(['t' => now()->timestamp]);
    }

    /**
     * @throws ValidationException
     */
    public static function validate(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+\-\s()]{7,40}$/'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'website' => ['nullable', 'max:0'],
            'company' => ['nullable', 'max:0'],
            'form_token' => ['required', 'string'],
        ], [
            'phone.regex' => 'Enter a valid phone number.',
            'website.max' => 'Unable to send this request.',
            'company.max' => 'Unable to send this request.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            try {
                $payload = decrypt((string) $request->input('form_token'));
                $issued = (int) ($payload['t'] ?? 0);
            } catch (DecryptException) {
                $validator->errors()->add('form_token', 'Please refresh the page and try again.');

                return;
            }

            $age = now()->timestamp - $issued;

            if ($age < 3) {
                $validator->errors()->add('form_token', 'Please wait a moment and try again.');
            }

            if ($age > 7200) {
                $validator->errors()->add('form_token', 'This form expired. Refresh the page and try again.');
            }

            $links = preg_match_all('/https?:\/\//i', (string) $request->input('message', '')) ?: 0;
            if ($links > 3) {
                $validator->errors()->add('message', 'Please remove extra links from your message.');
            }
        });

        return $validator->validate();
    }
}
