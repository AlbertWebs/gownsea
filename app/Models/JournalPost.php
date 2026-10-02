<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JournalPost extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'excerpt', 'body', 'image', 'status', 'published_at', 'author_id',
        'seo_title', 'seo_description', 'gallery',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'date', 'gallery' => 'array'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function (Builder $query) {
                $query->whereNull('published_at')->orWhereDate('published_at', '<=', now()->toDateString());
            });
    }

    /** @return array<string, mixed> */
    public function toPublicPost(): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'category' => $this->category,
            'date' => $this->published_at?->toDateString() ?? $this->created_at?->toDateString() ?? now()->toDateString(),
            'excerpt' => $this->excerpt,
            'image' => $this->image ?: ($this->gallery[0] ?? null),
            'images' => array_values(array_unique(array_filter(array_merge(
                $this->image ? [$this->image] : [],
                $this->gallery ?? [],
            )))),
            'body' => $this->body,
        ];
    }
}
