<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssistantRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'ip_address',
        'user_agent',
    ];
}
