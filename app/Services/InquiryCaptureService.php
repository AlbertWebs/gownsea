<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\AdminNotification;
use App\Models\Customer;
use App\Models\Inquiry;
use App\Models\Product;
use App\Support\Phone;
use Illuminate\Http\Request;

class InquiryCaptureService
{
    public function capture(array $validated, Request $request): Inquiry
    {
        $customer = $this->matchOrCreateCustomer($validated['name'], $validated['email'], $validated['phone']);
        $product = $this->detectProduct($validated['message'] ?? '', $request);

        $inquiry = Inquiry::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 1000),
            'type' => $product ? 'product' : 'general',
            'source' => $product ? 'catalogue' : 'website',
            'landing_url' => $request->headers->get('referer') ?: $request->url(),
            'product_id' => $product?->id,
            'customer_id' => $customer->id,
            'status' => 'new',
            'priority' => 'normal',
            'is_read' => false,
        ]);

        Activity::query()->create([
            'type' => 'note',
            'subject_type' => Inquiry::class,
            'subject_id' => $inquiry->id,
            'customer_id' => $customer->id,
            'title' => 'Inquiry received',
            'description' => $inquiry->message,
            'status' => 'completed',
        ]);

        AdminNotification::query()->create([
            'title' => 'New '.($product ? 'product' : 'general').' inquiry',
            'body' => $inquiry->name.' — '.$inquiry->email,
            'url' => '/admin/inquiries/'.($product ? 'products' : 'general').'/'.$inquiry->id,
        ]);

        return $inquiry;
    }

    public function matchOrCreateCustomer(string $name, ?string $email, ?string $phone): Customer
    {
        $normalized = Phone::normalize($phone);

        if (! $email && ! $normalized) {
            return Customer::query()->create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'status' => 'active',
            ]);
        }

        $customer = Customer::query()
            ->where(function ($query) use ($email, $normalized) {
                if ($email) {
                    $query->orWhere('email', $email);
                }
                if ($normalized) {
                    $query->orWhere('phone_normalized', $normalized);
                }
            })
            ->first();

        if ($customer) {
            $customer->fill(array_filter([
                'name' => $customer->name ?: $name,
                'email' => $customer->email ?: $email,
                'phone' => $customer->phone ?: $phone,
            ]))->save();

            return $customer;
        }

        return Customer::query()->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'status' => 'active',
        ]);
    }

    private function detectProduct(string $message, Request $request): ?Product
    {
        $haystack = $message.' '.($request->headers->get('referer') ?? '');

        $slugs = Product::query()->pluck('slug');
        foreach ($slugs as $slug) {
            if ($slug && str_contains($haystack, $slug)) {
                return Product::query()->where('slug', $slug)->first();
            }
        }

        return null;
    }
}
