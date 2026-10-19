<?php

namespace Tests\Unit;

use App\Models\Stock;
use App\Models\Warehousestore;
use App\Services\StockUnitService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UnusableUnitsHiddenTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        session()->put('activeStore', Warehousestore::find(1)->toArray());
    }

    private function stock(array $units): Stock
    {
        $stock = Stock::create(['name' => 'Unit Filter', 'selling_price' => 0, 'cost_price' => 0, 'type' => 'NORMAL']);
        $this->assertEmpty(StockUnitService::saveUnits($stock, $units));
        return $stock;
    }

    public function test_single_piece_product_only_exposes_pieces()
    {
        DB::beginTransaction();
        try {
            // cartons/packets defined but never priced
            $stock = $this->stock([
                1 => ['name' => 'Cartons', 'cost_price' => 0, 'selling_price' => 0],
                2 => ['name' => 'Packets', 'units_per_parent' => 10, 'cost_price' => 0, 'selling_price' => 0],
                3 => ['name' => 'Pieces', 'units_per_parent' => 10, 'cost_price' => 5, 'selling_price' => 8],
            ]);
            $units = StockUnitService::unitsForUi($stock->id, 234);
            $this->assertCount(1, $units);
            $this->assertEquals('Pieces', $units[0]['name']);
            $this->assertEquals(234, $units[0]['available']);
            $this->assertEquals('234 Pieces', StockUnitService::usableBreakdownLabel($stock->id, 234));
        } finally {
            DB::rollBack();
        }
    }

    public function test_priced_packaging_is_kept_and_multiplier_survives_hidden_middle_unit()
    {
        DB::beginTransaction();
        try {
            // packets unpriced (hidden) but cartons priced: carton = 10 packets = 100 pieces
            $stock = $this->stock([
                1 => ['name' => 'Cartons', 'cost_price' => 600, 'selling_price' => 800],
                2 => ['name' => 'Packets', 'units_per_parent' => 10, 'cost_price' => 0, 'selling_price' => 0],
                3 => ['name' => 'Pieces', 'units_per_parent' => 10, 'cost_price' => 5, 'selling_price' => 8],
            ]);
            $units = StockUnitService::unitsForUi($stock->id, 234);
            $this->assertEquals(['Cartons', 'Pieces'], array_column($units, 'name'));
            $this->assertEquals(100, $units[0]['multiplier']);
            $this->assertEquals(2, $units[0]['available']);
            $this->assertEquals('2 Cartons, 34 Pieces', StockUnitService::usableBreakdownLabel($stock->id, 234));

            // a unit already saved on a document stays visible when asked
            $packetId = collect(\App\Models\ProductUnit::where('stock_id', $stock->id)->get())->firstWhere('level', 2)->id;
            $this->assertCount(3, StockUnitService::unitsForUi($stock->id, 234, [$packetId]));
        } finally {
            DB::rollBack();
        }
    }
}
