<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $keys = ['company_name', 'phone', 'email', 'whatsapp', 'address', 'seo_title', 'seo_description', 'currency'];

        return view('admin.settings.edit', [
            'settings' => collect($keys)->mapWithKeys(fn ($key) => [
                $key => Setting::getValue($key, match ($key) {
                    'company_name' => config('gownsea.brand.name'),
                    'phone' => config('gownsea.brand.phone'),
                    'email' => config('gownsea.brand.email'),
                    'whatsapp' => config('gownsea.brand.whatsapp'),
                    'address' => config('gownsea.brand.address'),
                    'currency' => 'KES',
                    default => '',
                }),
            ]),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'currency' => ['required', 'string', 'max:8'],
        ]);

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
            Setting::setValue('brand.'.$key, $value);
        }

        return back()->with('status', 'Website settings saved.');
    }
}
