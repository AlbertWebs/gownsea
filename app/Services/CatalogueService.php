<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CatalogueService
{
    public function hasDatabaseCatalogue(): bool
    {
        try {
            return Product::query()->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function itemsByCategory(string $category): array
    {
        if ($this->hasDatabaseCatalogue()) {
            return Product::query()
                ->published()
                ->with(['category', 'images'])
                ->whereHas('category', fn ($q) => $q->where('slug', $category))
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->map(fn (Product $product) => $product->toStorefrontArray())
                ->all();
        }

        $featured = collect(config('gownsea.properties', []))->where('category', $category);
        $hire = collect(config('gownsea.hire_products', []))->where('category', $category);

        return $featured->merge($hire)->unique('slug')->values()->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function hireItems(): array
    {
        if ($this->hasDatabaseCatalogue()) {
            return Product::query()
                ->published()
                ->with(['category', 'images'])
                ->where('is_hire', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (Product $product) => $product->toStorefrontArray())
                ->all();
        }

        return config('gownsea.hire_products', []);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function featuredItems(): array
    {
        if ($this->hasDatabaseCatalogue()) {
            return Product::query()
                ->published()
                ->with(['category', 'images'])
                ->where('featured', true)
                ->orderBy('id')
                ->get()
                ->map(fn (Product $product) => $product->toStorefrontArray())
                ->all();
        }

        return config('gownsea.properties', []);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findBySlug(string $slug): ?array
    {
        if ($this->hasDatabaseCatalogue()) {
            $product = Product::query()
                ->with(['category', 'images'])
                ->where('slug', $slug)
                ->first();

            if ($product && $product->isPublic()) {
                $product->increment('views_count');

                return $product->toStorefrontArray();
            }

            if ($product) {
                return $product->toStorefrontArray();
            }
        }

        return collect(config('gownsea.hire_products', []))
            ->merge(config('gownsea.properties', []))
            ->firstWhere('slug', $slug);
    }

    /**
     * @param  array<string, mixed>  $property
     * @return array<string, mixed>
     */
    public function enrich(array $property): array
    {
        $profile = config('gownsea.product_profiles.'.$property['slug'], []);
        $image = $property['image'] ?? '/images/site/hero.webp';

        return array_merge([
            'gallery' => [$image],
            'options' => [
                'Size' => ['Small', 'Medium', 'Large', 'X-Large'],
                'Colour' => ['Black', 'Navy'],
            ],
            'details' => [
                'University-standard ceremonial finish',
                'Available for hire and purchase in Kenya',
                'Durable materials for a full ceremony day',
                'Nairobi fitting and delivery support',
            ],
            'about' => $property['description'] ?? '',
            'fit_note' => 'If you are unsure about sizing, choose the larger option or contact Gownsea for a quick fit check.',
            'size_guide' => [
                ['size' => 'Small', 'guide' => 'Petite frame; shorter gown length'],
                ['size' => 'Medium', 'guide' => 'Most common adult fit'],
                ['size' => 'Large', 'guide' => 'Taller frame or layered clothing'],
                ['size' => 'X-Large', 'guide' => 'Generous fit over a full gown'],
            ],
        ], $property, $profile);
    }

    public function brand(string $key, mixed $default = null): mixed
    {
        $fromSettings = \App\Models\Setting::getValue('brand.'.$key);
        if ($fromSettings !== null) {
            return $fromSettings;
        }

        return config('gownsea.brand.'.$key, $default);
    }

    public function category(string $slug): ?Category
    {
        return Category::query()->where('slug', $slug)->first();
    }

    public function categoryImage(string $slug, string $fallback = '/images/site/hero.webp'): string
    {
        return $this->category($slug)?->previewImage() ?: $fallback;
    }

    /**
     * @return array<int, array{src: string, label: string, headline: string}>
     */
    public function heroSlides(): array
    {
        $slides = [
            'graduation' => [
                'headline' => 'University-standard graduation gowns tailored for every ceremony.',
                'fallback' => '/images/site/hero.webp',
            ],
            'legal' => [
                'headline' => 'Courtroom-ready legal attire with a premium professional finish.',
                'fallback' => '/images/site/Amazon-seller-lawyer-renaldo-matamoro-86JiKaHF4I8-unsplash-min.jpg',
            ],
            'church' => [
                'headline' => 'Elegant church and choral wear designed for reverence and unity.',
                'fallback' => '/images/site/clergy-wear.webp',
            ],
        ];

        $categories = Category::query()->whereIn('slug', array_keys($slides))->get()->keyBy('slug');

        return collect($slides)->map(function (array $slide, string $slug) use ($categories) {
            $category = $categories->get($slug);

            return [
                'src' => $category?->previewImage() ?: $slide['fallback'],
                'label' => $category?->name ?: Str::headline($slug),
                'headline' => $slide['headline'],
            ];
        })->values()->all();
    }
}
