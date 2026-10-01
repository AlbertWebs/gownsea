<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductSellingPricesSeeder extends Seeder
{
    /**
     * Apply the prices visible in the WhatsApp catalogue screenshots captured on 2026-10-01.
     * For PP2 Graduation Gown, the struck-through KES 3,000 is the regular price and KES 2,500 is the sale price.
     * This map only includes catalogue items represented by existing product slugs.
     */
    public function run(): void
    {
        $prices = [
            'degree-graduation-gowns' => [6500, 'KES 6,500', null],
            'diploma-graduation-gowns' => [4500, 'KES 4,500', null],
            'graduation-stoles' => [1500, 'KES 1,500', null],
            'graduation-tassels' => [350, 'KES 350', null],
            'masters-gown' => [7500, 'KES 7,500', null],
            'phd-caps' => [4900, 'KES 4,900', null],
            'phd-graduation-gown' => [18500, 'KES 18,500', null],
            'preschool-graduation' => [3000, 'KES 3,000', 2500],
            'undergraduate-academic-hoods' => [1800, 'KES 1,800', null],
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
