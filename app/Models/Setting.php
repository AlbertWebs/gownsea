<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /** @var array<string, mixed>|null */
    protected static ?array $memo = null;

    public static function getValue(string $key, mixed $default = null): mixed
    {
        if (static::$memo === null) {
            static::$memo = static::query()->pluck('value', 'key')->all();
        }

        return static::$memo[$key] ?? $default;
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => is_array($value) ? json_encode($value) : $value]);
        static::$memo = null;
    }

    public static function logoPath(): ?string
    {
        $path = static::getValue('logo') ?: static::getValue('brand.logo');

        return is_string($path) && $path !== '' ? $path : null;
    }

    public static function logoUrl(): ?string
    {
        $path = static::logoPath();

        if ($path === null) {
            return null;
        }

        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
            ? $path
            : asset(ltrim($path, '/'));
    }
}
