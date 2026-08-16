<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Inquiry extends Model
{
    protected $table = 'assistant_requests';

    protected $fillable = [
        'name', 'email', 'phone', 'message', 'ip_address', 'user_agent',
        'type', 'source', 'landing_url', 'product_id', 'customer_id',
        'assigned_to', 'assigned_at', 'status', 'priority', 'follow_up_at',
        'tags', 'is_read', 'lead_id',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_read' => 'boolean',
            'assigned_at' => 'datetime',
            'follow_up_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }

    public function scopeProductType($query)
    {
        return $query->where('type', 'product');
    }

    public function scopeGeneralType($query)
    {
        return $query->where('type', '!=', 'product');
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->seesOnlyAssigned()) {
            $query->where('assigned_to', $user->id);
        }

        return $query;
    }
}
