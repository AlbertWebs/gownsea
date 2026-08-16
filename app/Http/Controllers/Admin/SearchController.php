<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q = trim($request->string('q')->toString());

        return view('admin.search', [
            'q' => $q,
            'products' => $q ? Product::query()->where('name', 'like', "%{$q}%")->orWhere('sku', 'like', "%{$q}%")->limit(8)->get() : collect(),
            'customers' => $q ? Customer::query()->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")->limit(8)->get() : collect(),
            'leads' => $q ? Lead::query()->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")->limit(8)->get() : collect(),
            'inquiries' => $q ? Inquiry::query()->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")->limit(8)->get() : collect(),
            'sales' => $q ? Sale::query()->where('number', 'like', "%{$q}%")->limit(8)->get() : collect(),
        ]);
    }
}
