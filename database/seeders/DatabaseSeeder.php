<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\Money;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@gownsea.com'],
            [
                'name' => 'Gownsea Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );

        $this->seedCategories();
        $this->seedProducts();
    }

    private function seedCategories(): void
    {
        foreach ([
            ['slug' => 'graduation', 'name' => 'Graduation Attire', 'sort_order' => 1],
            ['slug' => 'legal', 'name' => 'Legal Attire', 'sort_order' => 2],
            ['slug' => 'church', 'name' => 'Church Wear', 'sort_order' => 3],
        ] as $category) {
            Category::query()->updateOrCreate(['slug' => $category['slug']], $category + ['is_active' => true]);
        }
    }

    private function seedProducts(): void
    {
        $categories = Category::query()->pluck('id', 'slug');
        $items = collect(config('gownsea.hire_products', []))
            ->map(fn ($item) => $item + ['is_hire' => true, 'featured' => false])
            ->merge(collect(config('gownsea.properties', []))->map(fn ($item) => $item + ['is_hire' => false, 'featured' => true]))
            ->unique('slug');

        foreach ($items as $item) {
            $profile = config('gownsea.product_profiles.'.$item['slug'], []);
            $product = Product::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'category_id' => $categories[$item['category'] ?? 'graduation'] ?? null,
                    'sku' => 'GS-'.Str::upper(Str::substr(md5($item['slug']), 0, 8)),
                    'name' => $item['title'],
                    'short_description' => $item['description'] ?? null,
                    'description' => $profile['about'] ?? ($item['description'] ?? null),
                    'price_amount' => Money::parseLabel($item['price'] ?? null),
                    'price_label' => $item['price'] ?? null,
                    'availability' => 'in_stock',
                    'featured' => (bool) ($item['featured'] ?? false),
                    'status' => 'published',
                    'visibility' => 'public',
                    'image' => $item['image'] ?? null,
                    'cta' => $item['cta'] ?? 'Request Quote',
                    'location' => $item['location'] ?? 'Nairobi',
                    'url_path' => $item['url'] ?? '/product/'.$item['slug'],
                    'options' => $profile['options'] ?? null,
                    'details' => $profile['details'] ?? null,
                    'size_guide' => $profile['size_guide'] ?? null,
                    'fit_note' => $profile['fit_note'] ?? null,
                    'is_hire' => (bool) ($item['is_hire'] ?? false),
                ]
            );

            foreach ($profile['gallery'] ?? [] as $index => $path) {
                if ($path === $product->image) {
                    continue;
                }
                $product->images()->updateOrCreate(['path' => $path], ['sort_order' => $index]);
            }
        }
    }
}
