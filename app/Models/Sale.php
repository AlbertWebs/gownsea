<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number', 'customer_id', 'lead_id', 'salesperson_id', 'source',
        'status', 'payment_status', 'subtotal', 'discount', 'tax', 'total', 'notes',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function salesperson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'salesperson_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function recalculate(): void
    {
        $subtotal = (int) $this->items()->sum('line_total');
        $this->subtotal = $subtotal;
        $this->total = max(0, $subtotal - (int) $this->discount + (int) $this->tax);
        $this->save();
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->seesOnlyAssigned()) {
            $query->where('salesperson_id', $user->id);
        }

        return $query;
    }
}
