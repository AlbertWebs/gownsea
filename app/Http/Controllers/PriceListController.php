<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceListController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category:id,name')
            ->orderBy('name')
            ->get(['id', 'category_id', 'name', 'price_amount', 'sale_price_amount', 'hire_price_amount', 'is_hire']);

        return view('price-list.index', [
            'products' => $products,
            'unlocked' => request()->session()->boolean('price_list_unlocked'),
        ]);
    }

    public function unlock(Request $request): JsonResponse
    {
        $data = $request->validate(['pin' => ['required', 'string', 'size:6']]);
        $expectedHash = (string) config('services.price_list.pin_hash');

        if (! hash_equals($expectedHash, hash('sha256', $data['pin']))) {
            return response()->json(['message' => 'That PIN is incorrect. Please try again.'], 422);
        }

        $request->session()->regenerate();
        $request->session()->put('price_list_unlocked', true);

        return response()->json(['message' => 'Price list unlocked.']);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'purchase_price' => ['nullable', 'integer', 'min:0', 'max:99999999'],
            'hire_price' => ['nullable', 'integer', 'min:0', 'max:99999999'],
        ]);

        $product->price_amount = $data['purchase_price'] ?? null;
        $product->price_label = null;
        $product->hire_price_amount = $data['hire_price'] ?? null;
        $product->save();

        return response()->json([
            'message' => 'Prices saved successfully.',
            'purchase_price' => $product->price_amount,
            'hire_price' => $product->hire_price_amount,
        ]);
    }
}
