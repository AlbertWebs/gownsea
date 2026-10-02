<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'slug', 'sku', 'name', 'short_description', 'description',
        'price_amount', 'price_label', 'sale_price_amount', 'hire_price_amount', 'stock_quantity',
        'availability', 'featured', 'status', 'visibility', 'image', 'cta',
        'location', 'url_path', 'options', 'option_images', 'details', 'size_guide', 'fit_note',
        'seo_title', 'seo_description', 'seo_keywords', 'og_image', 'tags',
        'brand', 'min_order_qty', 'is_hire', 'sort_order', 'views_count',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'is_hire' => 'boolean',
            'options' => 'array',
            'option_images' => 'array',
            'details' => 'array',
            'size_guide' => 'array',
            'tags' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class, 'product_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function displayPrice(): string
    {
        if ($this->sale_price_amount !== null) {
            return Money::format($this->sale_price_amount);
        }

        return $this->price_label ?: Money::format($this->price_amount);
    }

    public function isPublic(): bool
    {
        return $this->status === 'published' && $this->visibility === 'public';
    }

    public function toStorefrontArray(): array
    {
        $gallery = $this->images->pluck('path')->filter()->values()->all();
        if ($this->image) {
            array_unshift($gallery, $this->image);
            $gallery = array_values(array_unique($gallery));
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->name,
            'location' => $this->location ?: 'Nairobi',
            'price' => $this->displayPrice(),
            'purchase_price' => Money::format($this->price_amount),
            'hire_price' => $this->hire_price_amount === null ? 'Hire price to be confirmed' : Money::format($this->hire_price_amount),
            'price_amount' => $this->price_amount,
            'sale_price_amount' => $this->sale_price_amount,
            'availability' => $this->availability,
            'is_hire' => $this->is_hire,
            'brand' => $this->brand,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'cta' => $this->cta ?: 'Request Quote',
            'description' => $this->short_description ?: $this->description,
            'category' => $this->category?->slug ?? 'graduation',
            'image' => $this->image ?: ($gallery[0] ?? '/images/site/hero.webp'),
            'url' => $this->url_path ?: '/product/'.$this->slug,
            'gallery' => $gallery ?: [$this->image ?: '/images/site/hero.webp'],
            'options' => $this->options ?: [],
            'option_images' => $this->option_images ?: [],
            'details' => $this->details ?: [],
            'about' => $this->description ?: $this->short_description,
            'fit_note' => $this->fit_note,
            'size_guide' => $this->size_guide ?: [],
            'sku' => $this->sku,
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('visibility', 'public');
    }
}
