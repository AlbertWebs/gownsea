<?php

namespace App\Support;

class Phone
{
    public static function normalize(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?: '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '254'.substr($digits, 1);
        }

        return $digits;
    }
}
