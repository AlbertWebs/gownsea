<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'parent_id', 'slug', 'name', 'description', 'image', 'sort_order',
        'is_active', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function previewImage(): ?string
    {
        if (is_string($this->image) && $this->image !== '') {
            return $this->image;
        }

        $fromProduct = $this->products()->whereNotNull('image')->where('image', '!=', '')->value('image');
        if (is_string($fromProduct) && $fromProduct !== '') {
            return $fromProduct;
        }

        return match ($this->slug) {
            'church' => '/images/site/clergy-wear.webp',
            'legal' => '/images/site/Amazon-seller-lawyer-renaldo-matamoro-86JiKaHF4I8-unsplash-min.jpg',
            'graduation' => '/images/site/graduation-attire.jpg',
            default => null,
        };
    }
}
