<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PriceCategory;
use App\Models\Stock;
use App\Models\StockPrice;

class LegacyPriceSeeder extends Seeder
{
    public function run()
    {
        // 1. Create standard base categories
        $packedBase = PriceCategory::firstOrCreate([
            'name' => 'Standard Retail Packed',
            'price_type' => 'packed',
            'status' => true
        ]);

        $yardBase = PriceCategory::firstOrCreate([
            'name' => 'Standard Retail Yard',
            'price_type' => 'yard',
            'status' => true
        ]);

        // 2. Populate stock_prices from existing records
        Stock::chunk(200, function ($stocks) use ($packedBase, $yardBase) {
            foreach ($stocks as $stock) {
                if ($stock->selling_price > 0) {
                    StockPrice::updateOrCreate(
                        ['stock_id' => $stock->id, 'price_category_id' => $packedBase->id],
                        ['price' => $stock->selling_price]
                    );
                }

                if ($stock->yard_selling_price > 0) {
                    StockPrice::updateOrCreate(
                        ['stock_id' => $stock->id, 'price_category_id' => $yardBase->id],
                        ['price' => $stock->yard_selling_price]
                    );
                }
            }
        });
    }
}
