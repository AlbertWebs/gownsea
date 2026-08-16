<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference', 'customer_id', 'product_id', 'inquiry_id', 'assigned_to',
        'name', 'company', 'email', 'phone', 'source', 'estimated_value',
        'probability', 'stage', 'priority', 'next_follow_up_at', 'notes',
        'won_at', 'lost_at', 'lost_reason',
    ];

    protected function casts(): array
    {
        return [
            'next_follow_up_at' => 'datetime',
            'won_at' => 'datetime',
            'lost_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class)->latest();
    }

    public function timeline(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function weightedForecast(): int
    {
        return (int) round($this->estimated_value * ($this->probability / 100));
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->seesOnlyAssigned()) {
            $query->where('assigned_to', $user->id);
        }

        return $query;
    }
}
