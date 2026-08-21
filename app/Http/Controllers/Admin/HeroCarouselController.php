<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HeroCarouselController extends Controller
{
    public function edit(): View
    {
        return view('admin.hero-carousel.edit', [
            'slides' => Setting::heroSlideRecords(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'slides' => ['required', 'array', 'min:1', 'max:6'],
            'slides.*.label' => ['required', 'string', 'max:80'],
            'slides.*.headline' => ['required', 'string', 'max:220'],
            'slides.*.category' => ['nullable', 'string', 'max:80'],
            'slides.*.src' => ['nullable', 'string', 'max:255'],
            'slides.*.image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'slides.*.remove_image' => ['nullable', 'boolean'],
        ]);

        $existing = Setting::heroSlideRecords();
        $saved = [];

        foreach ($data['slides'] as $index => $slide) {
            $currentSrc = trim((string) ($slide['src'] ?? ''));
            if ($currentSrc === '' && isset($existing[$index]['src'])) {
                $currentSrc = (string) $existing[$index]['src'];
            }

            if ($request->boolean("slides.$index.remove_image") && ! $request->hasFile("slides.$index.image")) {
                $this->deleteHeroImage($currentSrc);
                $currentSrc = '';
            }

            if ($request->hasFile("slides.$index.image")) {
                $file = $request->file("slides.$index.image");
                $directory = public_path('images/hero');
                if (! is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $name = 'hero-'.($index + 1).'-'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $file->move($directory, $name);
                $this->deleteHeroImage($currentSrc);
                $currentSrc = '/images/hero/'.$name;
            }

            $saved[] = [
                'label' => $slide['label'],
                'headline' => $slide['headline'],
                'src' => $currentSrc,
                'category' => $slide['category'] ?? null,
            ];
        }

        // Clean up images from removed slides
        $kept = collect($saved)->pluck('src')->filter()->all();
        foreach ($existing as $old) {
            $oldSrc = (string) ($old['src'] ?? '');
            if ($oldSrc !== '' && ! in_array($oldSrc, $kept, true)) {
                $this->deleteHeroImage($oldSrc);
            }
        }

        Setting::setHeroSlides($saved);

        return back()->with('status', 'Hero carousel saved.');
    }

    private function deleteHeroImage(?string $path): void
    {
        if (! is_string($path) || $path === '') {
            return;
        }

        $relative = ltrim($path, '/');
        if (! str_starts_with($relative, 'images/hero/')) {
            return;
        }

        $full = public_path($relative);
        if (is_file($full)) {
            unlink($full);
        }
    }
}
