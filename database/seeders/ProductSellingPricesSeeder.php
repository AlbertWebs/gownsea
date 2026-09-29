<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductSellingPricesSeeder extends Seeder
{
    /**
     * Apply the selling prices from the 2026-09-29 product export.
     * Price amount is the regular price; sale price is nullable when the export had no sale price.
     */
    public function run(): void
    {
        $prices = [
            'bachelors-graduation-gown-cap-hood-set' => [7500, 'KES 7,500', 6500],
            'certificate-gowns' => [2500, 'KES 2,500', 2500],
            'advocates-shirt' => [2600, 'KES 2,600', 2600],
            'degree-graduation-gowns' => [7500, 'KES 7,500', 6500],
            'diploma-graduation-gowns' => [5000, 'KES 5,000', 4800],
            'graduation-stoles' => [1500, 'KES 1,500', null],
            'graduation-tassels' => [350, 'KES 350', 350],
            'masters-gown' => [8500, 'KES 8,500', null],
            'phd-caps' => [5000, 'KES 5,000', 4500],
            'phd-graduation-gown' => [18500, 'KES 18,500', 18500],
            'plain-english-bib' => [500, 'KES 500', null],
            'preschool-graduation' => [3500, 'KES 3,500', 3000],
            'undergraduate-academic-hoods' => [2500, 'KES 2,500', 2500],
        ];

        DB::transaction(function () use ($prices): void {
            foreach ($prices as $slug => [$amount, $label, $saleAmount]) {
                $updated = DB::table('products')
                    ->where('slug', $slug)
                    ->update([
                        'price_amount' => $amount,
                        'price_label' => $label,
                        'sale_price_amount' => $saleAmount,
                        'updated_at' => now(),
                    ]);

                if ($updated !== 1) {
                    throw new RuntimeException("Expected one product row for [{$slug}], updated {$updated}.");
                }
            }
        });

        $this->command?->info('Updated selling prices for '.count($prices).' catalogue products.');
    }
}
