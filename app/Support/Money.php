<?php

namespace App\Support;

class Money
{
    public static function parseLabel(?string $label): ?int
    {
        if (! $label) {
            return null;
        }

        if (! preg_match('/([\d,]+)/', $label, $matches)) {
            return null;
        }

        return (int) str_replace(',', '', $matches[1]);
    }

    public static function format(?int $amount): string
    {
        if ($amount === null) {
            return 'Request quote';
        }

        return 'KES '.number_format($amount);
    }
}
