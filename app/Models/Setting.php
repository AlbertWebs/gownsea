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

    public static function mobileNavEnabled(): bool
    {
        $value = static::getValue('mobile_nav_enabled');

        if ($value === null || $value === '') {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return array<int, array{label: string, headline: string, src: string, category: string|null}>
     */
    public static function defaultHeroSlides(): array
    {
        return [
            [
                'label' => 'Graduation',
                'headline' => 'University-standard graduation gowns tailored for every ceremony.',
                'src' => '',
                'category' => 'graduation',
            ],
            [
                'label' => 'Legal',
                'headline' => 'Courtroom-ready legal attire with a premium professional finish.',
                'src' => '',
                'category' => 'legal',
            ],
            [
                'label' => 'Church',
                'headline' => 'Elegant church and choral wear designed for reverence and unity.',
                'src' => '',
                'category' => 'church',
            ],
        ];
    }

    /**
     * @return array<int, array{label: string, headline: string, src: string, category: string|null}>
     */
    public static function heroSlideRecords(): array
    {
        $raw = static::getValue('hero_slides');
        $decoded = null;

        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
        } elseif (is_array($raw)) {
            $decoded = $raw;
        }

        if (! is_array($decoded) || $decoded === []) {
            return static::defaultHeroSlides();
        }

        return collect($decoded)
            ->map(function ($slide) {
                if (! is_array($slide)) {
                    return null;
                }

                $label = trim((string) ($slide['label'] ?? ''));
                $headline = trim((string) ($slide['headline'] ?? ''));
                $src = trim((string) ($slide['src'] ?? ''));
                $category = isset($slide['category']) ? trim((string) $slide['category']) : null;

                if ($label === '' && $headline === '' && $src === '') {
                    return null;
                }

                return [
                    'label' => $label !== '' ? $label : 'Featured',
                    'headline' => $headline,
                    'src' => $src,
                    'category' => $category !== '' ? $category : null,
                ];
            })
            ->filter()
            ->values()
            ->all() ?: static::defaultHeroSlides();
    }

    /**
     * @param  array<int, array{label?: string, headline?: string, src?: string, category?: string|null}>  $slides
     */
    public static function setHeroSlides(array $slides): void
    {
        $normalized = collect($slides)
            ->map(fn ($slide) => [
                'label' => trim((string) ($slide['label'] ?? '')),
                'headline' => trim((string) ($slide['headline'] ?? '')),
                'src' => trim((string) ($slide['src'] ?? '')),
                'category' => filled($slide['category'] ?? null) ? trim((string) $slide['category']) : null,
            ])
            ->filter(fn ($slide) => $slide['label'] !== '' || $slide['headline'] !== '' || $slide['src'] !== '')
            ->values()
            ->all();

        static::setValue('hero_slides', $normalized);
    }
}
