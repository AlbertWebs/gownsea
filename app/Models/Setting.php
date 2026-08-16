<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $all = Cache::remember('app_settings', 60, function () {
            return static::query()->pluck('value', 'key')->all();
        });

        return $all[$key] ?? $default;
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => is_array($value) ? json_encode($value) : $value]);
        Cache::forget('app_settings');
    }
}
