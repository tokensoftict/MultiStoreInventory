<?php

namespace Tests\Unit;

use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\StockQuantityAdjustment;
use App\Models\Warehousestore;
use App\Services\StockUnitService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MultiLevelQuickAdjustTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        session()->put('activeStore', Warehousestore::find(1)->toArray());
    }

    private function makeStock(): Stock
    {
        $stock = Stock::create(['name' => 'Adjust Test', 'selling_price' => 0, 'cost_price' => 0, 'type' => 'NORMAL']);
        $errors = StockUnitService::saveUnits($stock, [
            1 => ['name' => 'Cartons', 'cost_price' => 600, 'selling_price' => 800],
            2 => ['name' => 'Packets', 'units_per_parent' => 10, 'cost_price' => 60, 'selling_price' => 80],
            3 => ['name' => 'Pieces', 'units_per_parent' => 10, 'cost_price' => 6, 'selling_price' => 8],
        ]);
        $this->assertEmpty($errors);
        $col = getActiveStore()->yard_column;
        $stock->stockbatches()->create(['received_date' => date('Y-m-d'), $col => 40]);
        $stock->stockbatches()->create(['received_date' => date('Y-m-d'), $col => 60]);
        return $stock;
    }

    public function test_adjust_sets_total_from_all_units_and_logs_it()
    {
        DB::beginTransaction();
        try {
            $stock = $this->makeStock();
            $errors = StockUnitService::adjustQuantity($stock, [1 => 2, 2 => 3, 3 => 4]);
            $this->assertEmpty($errors);

            $col = getActiveStore()->yard_column;
            // 2 cartons (200) + 3 packets (30) + 4 pieces = 234, stored in a single batch
            $this->assertEquals(234, (float)$stock->stockbatches()->sum($col));

            $log = StockQuantityAdjustment::where('stock_id', $stock->id)->first();
            $this->assertEquals(100, $log->from);
            $this->assertEquals(234, $log->to);
            $this->assertEquals($col, $log->changed_column);
        } finally {
            DB::rollBack();
        }
    }

    public function test_adjust_can_zero_out_and_rejects_negatives()
    {
        DB::beginTransaction();
        try {
            $stock = $this->makeStock();
            $col = getActiveStore()->yard_column;

            $this->assertNotEmpty(StockUnitService::adjustQuantity($stock, [1 => -1]));
            $this->assertEquals(100, (float)$stock->stockbatches()->sum($col));

            $this->assertEmpty(StockUnitService::adjustQuantity($stock, [1 => 0, 2 => 0, 3 => 0]));
            $this->assertEquals(0, (float)$stock->stockbatches()->sum($col));
        } finally {
            DB::rollBack();
        }
    }

    public function test_adjust_fails_without_units_or_batches()
    {
        DB::beginTransaction();
        try {
            $bare = Stock::create(['name' => 'No Units', 'selling_price' => 0, 'cost_price' => 0, 'type' => 'NORMAL']);
            $this->assertNotEmpty(StockUnitService::adjustQuantity($bare, [3 => 5]));

            $noBatch = Stock::create(['name' => 'No Batch', 'selling_price' => 0, 'cost_price' => 0, 'type' => 'NORMAL']);
            StockUnitService::saveUnits($noBatch, [
                2 => ['name' => 'Packets', 'cost_price' => 1, 'selling_price' => 2],
                3 => ['name' => 'Pieces', 'units_per_parent' => 10, 'cost_price' => 1, 'selling_price' => 1],
            ]);
            $this->assertNotEmpty(StockUnitService::adjustQuantity($noBatch, [3 => 5]));
        } finally {
            DB::rollBack();
        }
    }
}
