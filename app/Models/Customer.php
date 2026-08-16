<?php

namespace App\Models;

use App\Support\Phone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'company', 'email', 'phone', 'phone_normalized', 'address', 'status', 'notes',
    ];

    protected static function booted(): void
    {
        static::saving(function (Customer $customer): void {
            $customer->phone_normalized = Phone::normalize($customer->phone);
        });
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
