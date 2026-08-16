<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    protected $fillable = [
        'type', 'subject_type', 'subject_id', 'customer_id', 'lead_id',
        'user_id', 'title', 'description', 'due_at', 'status', 'outcome', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_at && $this->due_at->isPast();
    }
}
