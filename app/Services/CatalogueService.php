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
                ->map(fn (Product $product) => $this->storefrontArray($product))
                ->all();
        }

        $featured = collect(config('gownsea.properties', []))->where('category', $category);
        $hire = collect(config('gownsea.hire_products', []))->where('category', $category);

        return $featured->merge($hire)->unique('slug')->map(fn (array $item) => $this->applyProfileSummary($this->applySalePrice($item)))->values()->all();
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
                ->map(fn (Product $product) => $this->storefrontArray($product))
                ->all();
        }

        return collect(config('gownsea.hire_products', []))
            ->map(fn (array $item) => $this->applyProfileSummary($this->applySalePrice($item)))
            ->all();
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
                ->map(fn (Product $product) => $this->storefrontArray($product))
                ->all();
        }

        return collect(config('gownsea.properties', []))
            ->map(fn (array $item) => $this->applyProfileSummary($this->applySalePrice($item)))
            ->all();
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

                return $this->storefrontArray($product);
            }

            abort_if($product, 404);
        }

        $property = collect(config('gownsea.hire_products', []))
            ->merge(config('gownsea.properties', []))
            ->firstWhere('slug', $slug);

        return $property ? $this->applySalePrice($property) : null;
    }

    /**
     * @param  array<string, mixed>  $property
     * @return array<string, mixed>
     */
    public function enrich(array $property): array
    {
        $property = $this->applySalePrice($property);
        $slug = (string) ($property['slug'] ?? '');
        $profile = config('gownsea.product_profiles.'.$slug, []);
        $image = $property['image'] ?? '/images/site/hero.webp';

        $enriched = array_merge([
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
        ], $profile, $property);

        $enriched = $this->applyProfileSummary($enriched);
        $about = trim(strip_tags((string) ($property['about'] ?? '')));
        $legacyHoodCopy = $slug === 'undergraduate-academic-hoods'
            && str_starts_with($about, 'Your graduation look is not complete without the academic hood.');

        if (filled($profile['about'] ?? null) && ($about === '' || $this->matchesCatalogueDescription($slug, $about) || $legacyHoodCopy)) {
            $enriched['about'] = $profile['about'];
        }

        $details = $property['details'] ?? [];
        $legacyHoodDetails = $slug === 'undergraduate-academic-hoods'
            && ($details[0] ?? null) === 'Traditional undergraduate academic hood';

        if (empty($details) || $legacyHoodDetails) {
            $enriched['details'] = $profile['details'] ?? $enriched['details'];
        }

        return $enriched;
    }

    /** @return array<string, mixed> */
    private function storefrontArray(Product $product): array
    {
        return $this->applyProfileSummary($product->toStorefrontArray());
    }

    /** @param array<string, mixed> $item @return array<string, mixed> */
    private function applyProfileSummary(array $item): array
    {
        $summary = config('gownsea.product_profiles.'.($item['slug'] ?? '').'.description');
        $current = trim(strip_tags((string) ($item['description'] ?? '')));

        if (filled($summary) && ($current === '' || $this->matchesCatalogueDescription((string) ($item['slug'] ?? ''), $current))) {
            $item['description'] = $summary;
        }

        return $item;
    }

    private function matchesCatalogueDescription(string $slug, string $description): bool
    {
        $baseline = collect(config('gownsea.hire_products', []))
            ->merge(config('gownsea.properties', []))
            ->firstWhere('slug', $slug)['description'] ?? null;

        return is_string($baseline) && trim(strip_tags($baseline)) === trim($description);
    }

    /** @param array<string, mixed> $item */
    private function applySalePrice(array $item): array
    {
        if (filled($item['sale_price'] ?? null)) {
            $item['price'] = $item['sale_price'];
        }

        return $item;
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
     * @param  array{src?: string, category?: string|null}  $slide
     */
    public function resolveHeroSlideImage(array $slide): string
    {
        $fallbacks = [
            'graduation' => '/images/site/hero.webp',
            'legal' => '/images/site/Amazon-seller-lawyer-renaldo-matamoro-86JiKaHF4I8-unsplash-min.jpg',
            'church' => '/images/site/clergy-wear.webp',
        ];

        $categorySlug = $slide['category'] ?? null;
        $fallback = $categorySlug && isset($fallbacks[$categorySlug])
            ? $fallbacks[$categorySlug]
            : '/images/site/hero.webp';

        if (filled($slide['src'] ?? null)) {
            return (string) $slide['src'];
        }

        return $categorySlug ? $this->categoryImage($categorySlug, $fallback) : $fallback;
    }

    /**
     * @return array<int, array{src: string, label: string, headline: string}>
     */
    public function heroSlides(): array
    {
        return collect(\App\Models\Setting::heroSlideRecords())
            ->map(function (array $slide) {
                $categorySlug = $slide['category'] ?? null;

                return [
                    'src' => $this->resolveHeroSlideImage($slide),
                    'label' => $slide['label'] ?: ($categorySlug ? Str::headline($categorySlug) : 'Featured'),
                    'headline' => $slide['headline'] ?: 'Premium ceremonial attire from Gownsea.',
                ];
            })
            ->values()
            ->all();
    }
}
