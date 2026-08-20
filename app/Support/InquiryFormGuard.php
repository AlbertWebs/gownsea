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
     * @return array{prompt: string, token: string}
     */
    public static function mathChallenge(): array
    {
        $left = random_int(3, 12);
        $right = random_int(2, 9);

        if (random_int(0, 1) === 1) {
            $prompt = $left.' + '.$right;
            $expected = $left + $right;
        } else {
            $min = min($left, $right);
            $max = max($left, $right);
            $prompt = $max.' − '.$min;
            $expected = $max - $min;
        }

        return [
            'prompt' => $prompt,
            'token' => encrypt([
                'exp' => $expected,
                't' => now()->timestamp,
            ]),
        ];
    }

    /**
     * @throws ValidationException
     */
    public static function validate(Request $request): array
    {
        $requiresMath = $request->input('form_intent') === 'bulk' || $request->filled('math_token');

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+\-\s()]{7,40}$/'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
            'website' => ['nullable', 'max:0'],
            'company' => ['nullable', 'max:0'],
            'form_token' => ['required', 'string'],
            'form_intent' => ['nullable', 'string', 'in:bulk'],
        ];

        if ($requiresMath) {
            $rules['math_token'] = ['required', 'string'];
            $rules['math_answer'] = ['required', 'string', 'max:8'];
        }

        $validator = Validator::make($request->all(), $rules, [
            'phone.regex' => 'Enter a valid phone number.',
            'website.max' => 'Unable to send this request.',
            'company.max' => 'Unable to send this request.',
            'math_answer.required' => 'Solve the short maths check to send your inquiry.',
        ]);

        $validator->after(function ($validator) use ($request, $requiresMath): void {
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

            if ($requiresMath) {
                try {
                    $math = decrypt((string) $request->input('math_token'));
                    $expected = (int) ($math['exp'] ?? -1);
                } catch (DecryptException) {
                    $validator->errors()->add('math_answer', 'Please refresh the page and try the maths check again.');

                    return;
                }

                $given = preg_replace('/\s+/', '', (string) $request->input('math_answer', ''));

                if (! is_numeric($given) || (int) $given !== $expected) {
                    $validator->errors()->add('math_answer', 'That answer is not correct. Try again.');
                }
            }
        });

        $validated = $validator->validate();

        unset($validated['math_token'], $validated['math_answer'], $validated['form_intent']);

        return $validated;
    }
}
