<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CatalogueFeedBuilder
{
    /**
     * @return Collection<int, array<string, string>>
     */
    public function googleRows(): Collection
    {
        return $this->products()->map(function (Product $product) {
            $currency = strtoupper((string) Setting::getValue('currency', Setting::getValue('brand.currency', 'KES')));
            $price = $this->formatMoney($product->price_amount, $currency);
            $salePrice = $product->sale_price_amount
                ? $this->formatMoney($product->sale_price_amount, $currency)
                : '';

            $images = $this->imageUrls($product);
            $primary = array_shift($images) ?: url('/images/site/hero.webp');

            return [
                'id' => (string) ($product->sku ?: 'GS-'.$product->id),
                'title' => Str::limit(strip_tags((string) $product->name), 150, ''),
                'description' => Str::limit($this->plainText($product->short_description ?: $product->description ?: $product->name), 5000, ''),
                'link' => $this->productUrl($product),
                'image_link' => $primary,
                'additional_image_link' => implode(',', array_slice($images, 0, 10)),
                'availability' => $this->availability($product),
                'price' => $price,
                'sale_price' => $salePrice,
                'brand' => (string) ($product->brand ?: Setting::getValue('company_name', config('gownsea.brand.name', 'Gownsea'))),
                'condition' => 'new',
                'product_type' => (string) ($product->category?->name ?: 'Apparel & Accessories'),
                'google_product_category' => 'Apparel & Accessories > Clothing',
                'identifier_exists' => 'false',
                'mpn' => (string) ($product->sku ?: 'GS-'.$product->id),
                'item_group_id' => (string) ($product->category?->slug ?: 'gownsea'),
                'custom_label_0' => $product->is_hire ? 'hire' : 'sale',
                'custom_label_1' => (string) $product->status,
            ];
        })->values();
    }

    /**
     * @return Collection<int, array<string, string>>
     */
    public function facebookRows(): Collection
    {
        return $this->products()->map(function (Product $product) {
            $currency = strtoupper((string) Setting::getValue('currency', Setting::getValue('brand.currency', 'KES')));
            $price = $this->formatMoney($product->price_amount, $currency);
            $salePrice = $product->sale_price_amount
                ? $this->formatMoney($product->sale_price_amount, $currency)
                : '';

            $images = $this->imageUrls($product);
            $primary = array_shift($images) ?: url('/images/site/hero.webp');

            return [
                'id' => (string) ($product->sku ?: 'GS-'.$product->id),
                'title' => Str::limit(strip_tags((string) $product->name), 200, ''),
                'description' => Str::limit($this->plainText($product->short_description ?: $product->description ?: $product->name), 5000, ''),
                'availability' => $this->availability($product),
                'condition' => 'new',
                'price' => $price,
                'sale_price' => $salePrice,
                'link' => $this->productUrl($product),
                'image_link' => $primary,
                'additional_image_link' => implode(',', array_slice($images, 0, 10)),
                'brand' => (string) ($product->brand ?: Setting::getValue('company_name', config('gownsea.brand.name', 'Gownsea'))),
                'google_product_category' => 'Apparel & Accessories > Clothing',
                'fb_product_category' => 'clothing & accessories > clothing',
                'quantity_to_sell_on_facebook' => (string) max(0, (int) ($product->stock_quantity ?? 1)),
                'product_type' => (string) ($product->category?->name ?: 'Ceremonial attire'),
                'custom_label_0' => $product->is_hire ? 'hire' : 'sale',
            ];
        })->values();
    }

    /**
     * @return Collection<int, Product>
     */
    private function products(): Collection
    {
        return Product::query()
            ->with(['category', 'images'])
            ->published()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function productUrl(Product $product): string
    {
        $path = $product->url_path ?: '/product/'.$product->slug;

        return url($path);
    }

    /**
     * @return list<string>
     */
    private function imageUrls(Product $product): array
    {
        $paths = collect([$product->image])
            ->merge($product->images->pluck('path'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return array_map(function (string $path) {
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }

            return url('/'.ltrim($path, '/'));
        }, $paths);
    }

    private function availability(Product $product): string
    {
        return match ($product->availability) {
            'out_of_stock', 'sold_out' => 'out_of_stock',
            'preorder' => 'preorder',
            default => 'in_stock',
        };
    }

    private function formatMoney(?int $amount, string $currency): string
    {
        return number_format(max(0, (int) $amount), 2, '.', '').' '.$currency;
    }

    private function plainText(?string $value): string
    {
        $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    }
}
