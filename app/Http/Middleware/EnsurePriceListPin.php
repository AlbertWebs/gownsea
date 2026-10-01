<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePriceListPin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->boolean('price_list_unlocked')) {
            return response()->json(['message' => 'Enter the price list PIN to continue.'], 401);
        }

        return $next($request);
    }
}
