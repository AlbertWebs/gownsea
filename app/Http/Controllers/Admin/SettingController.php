<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $keys = ['company_name', 'phone', 'email', 'whatsapp', 'address', 'seo_title', 'seo_description', 'currency', 'logo', 'mobile_nav_enabled'];

        return view('admin.settings.edit', [
            'settings' => collect($keys)->mapWithKeys(fn ($key) => [
                $key => Setting::getValue($key, match ($key) {
                    'company_name' => config('gownsea.brand.name'),
                    'phone' => config('gownsea.brand.phone'),
                    'email' => config('gownsea.brand.email'),
                    'whatsapp' => config('gownsea.brand.whatsapp'),
                    'address' => config('gownsea.brand.address'),
                    'currency' => 'KES',
                    'mobile_nav_enabled' => '1',
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
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'mobile_nav_enabled' => ['nullable', 'boolean'],
        ]);

        $currentLogo = Setting::logoPath();

        if ($request->boolean('remove_logo') && ! $request->hasFile('logo')) {
            $this->deleteLogoFile($currentLogo);
            $data['logo'] = '';
        } elseif ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $directory = public_path('images/brand');
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $name = 'logo-'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move($directory, $name);
            $this->deleteLogoFile($currentLogo);
            $data['logo'] = '/images/brand/'.$name;
        } else {
            unset($data['logo']);
        }

        unset($data['remove_logo']);

        $mobileNavEnabled = $request->boolean('mobile_nav_enabled');
        unset($data['mobile_nav_enabled']);

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
            Setting::setValue('brand.'.$key, $value);
        }

        Setting::setValue('mobile_nav_enabled', $mobileNavEnabled ? '1' : '0');

        return back()->with('status', 'Website settings saved.');
    }

    private function deleteLogoFile(?string $path): void
    {
        if (! is_string($path) || $path === '') {
            return;
        }

        $relative = ltrim($path, '/');
        if (! str_starts_with($relative, 'images/brand/')) {
            return;
        }

        $full = public_path($relative);
        if (is_file($full)) {
            unlink($full);
        }
    }
}
