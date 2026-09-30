<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignLink extends Model
{
    protected $fillable = [
        'name', 'destination_path', 'destination_label', 'utm_source', 'utm_medium',
        'utm_campaign', 'utm_content', 'utm_term', 'created_by',
    ];

    public function trackedUrl(): string
    {
        $parameters = array_filter([
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_content' => $this->utm_content,
            'utm_term' => $this->utm_term,
        ], fn ($value) => filled($value));

        return url($this->destination_path).'?'.http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
    }
}
