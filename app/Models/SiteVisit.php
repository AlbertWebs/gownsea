<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'visitor_hash', 'path', 'source', 'referrer_host', 'referrer_path', 'device',
        'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'created_at',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }
}
