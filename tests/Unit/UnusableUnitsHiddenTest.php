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

    public function test_pieces_with_zero_selling_price_is_hidden()
    {
        DB::beginTransaction();
        try {
            // Pieces defined with selling_price 0 - only Cartons and Packets priced
            $stock = $this->stock([
                1 => ['name' => 'Cartons', 'cost_price' => 600, 'selling_price' => 800],
                2 => ['name' => 'Packets', 'units_per_parent' => 10, 'cost_price' => 50, 'selling_price' => 80],
                3 => ['name' => 'Pieces', 'units_per_parent' => 10, 'cost_price' => 5, 'selling_price' => 0],
            ]);
            $units = StockUnitService::unitsForUi($stock->id, 234);
            $this->assertEquals(['Cartons', 'Packets'], array_column($units, 'name'));
            $this->assertNotContains('Pieces', array_column($units, 'name'));
        } finally {
            DB::rollBack();
        }
    }

    public function test_pieces_only_product_allows_zero_or_empty_carton_and_packet_information()
    {
        DB::beginTransaction();
        try {
            // User creates a product sold only as pieces:
            // Carton prices are 0, Packets in 1 Cartons is 0, Packet prices 0, Pieces in Packet is 0.
            // Only Pieces cost and selling price are provided.
            $stock = Stock::create(['name' => 'Single Piece Product', 'selling_price' => 0, 'cost_price' => 0, 'type' => 'NORMAL']);
            $formUnits = [
                1 => ['name' => 'Cartons', 'units_per_parent' => 1, 'cost_price' => 0, 'selling_price' => 0],
                2 => ['name' => 'Packets', 'units_per_parent' => 0, 'cost_price' => '', 'selling_price' => 0],
                3 => ['name' => 'Pieces', 'units_per_parent' => 0, 'cost_price' => 50, 'selling_price' => 100],
            ];

            $errors = StockUnitService::saveUnits($stock, $formUnits);
            $this->assertEmpty($errors, 'Saving pieces-only units with zero conversions/prices for cartons & packets should succeed without errors.');

            $activeUnits = \App\Models\ProductUnit::where('stock_id', $stock->id)->where('status', 1)->get();
            $this->assertCount(1, $activeUnits);
            $pieceUnit = $activeUnits->first();
            $this->assertEquals(3, $pieceUnit->level);
            $this->assertEquals('Pieces', $pieceUnit->name);
            $this->assertNull($pieceUnit->parent_unit_id);
            $this->assertNull($pieceUnit->units_per_parent);
            $this->assertEquals(50, $pieceUnit->cost_price);
            $this->assertEquals(100, $pieceUnit->selling_price);

            // Stock legacy/aggregate prices reflect the piece price
            $stock->refresh();
            $this->assertEquals(100, $stock->selling_price);
            $this->assertEquals(50, $stock->cost_price);
            $this->assertEquals(100, $stock->yard_selling_price);
            $this->assertEquals(50, $stock->yard_cost_price);

            // UI units expose only Pieces
            $uiUnits = StockUnitService::unitsForUi($stock->id, 50);
            $this->assertCount(1, $uiUnits);
            $this->assertEquals('Pieces', $uiUnits[0]['name']);
            $this->assertEquals(1, $uiUnits[0]['multiplier']);
            $this->assertEquals(50, $uiUnits[0]['available']);
            $this->assertEquals('50 Pieces', StockUnitService::breakdownLabel($stock->id, 50));
        } finally {
            DB::rollBack();
        }
    }

    public function test_stock_controller_store_and_update_with_pieces_only()
    {
        DB::beginTransaction();
        try {
            $controller = app(\App\Http\Controllers\StockManager\StockController::class);
            $request = \Illuminate\Http\Request::create('/stock/store', 'POST', [
                'name' => 'Controller Pieces Only',
                'status' => 1,
                'type' => 'NORMAL',
                'units' => [
                    1 => ['name' => 'Cartons', 'units_per_parent' => 1, 'cost_price' => 0, 'selling_price' => 0],
                    2 => ['name' => 'Packets', 'units_per_parent' => 0, 'cost_price' => '', 'selling_price' => 0],
                    3 => ['name' => 'Pieces', 'units_per_parent' => 0, 'cost_price' => 30, 'selling_price' => 60],
                ],
                'stock_batch' => [
                    'units_qty' => [
                        1 => '',
                        2 => '0',
                        3 => '20',
                    ],
                ],
            ]);

            $response = $controller->store($request);
            $this->assertTrue($response->isRedirection());

            $stock = Stock::where('name', 'Controller Pieces Only')->first();
            $this->assertNotNull($stock);
            $this->assertEquals(60, $stock->selling_price);
            $this->assertEquals(30, $stock->cost_price);
            $this->assertEquals(60, $stock->yard_selling_price);
            $this->assertEquals(30, $stock->yard_cost_price);

            // Batch created with 20 base pieces
            $batch = $stock->stockbatches()->first();
            $this->assertNotNull($batch);
            $store = getActiveStore();
            $this->assertEquals(20, $batch->{$store->yard_column});

            // Update: change piece price and verify unit prices are preserved
            $updateRequest = \Illuminate\Http\Request::create('/stock/update/' . $stock->id, 'POST', [
                'name' => 'Controller Pieces Only Updated',
                'status' => 1,
                'type' => 'NORMAL',
                'units' => [
                    1 => ['name' => 'Cartons', 'units_per_parent' => 1, 'cost_price' => 0, 'selling_price' => 0],
                    2 => ['name' => 'Packets', 'units_per_parent' => 0, 'cost_price' => 0, 'selling_price' => 0],
                    3 => ['name' => 'Pieces', 'units_per_parent' => 0, 'cost_price' => 40, 'selling_price' => 75],
                ],
            ]);

            $updateResponse = $controller->update($updateRequest, $stock->id);
            $this->assertTrue($updateResponse->isRedirection());

            $stock->refresh();
            $this->assertEquals('Controller Pieces Only Updated', $stock->name);
            $this->assertEquals(75, $stock->selling_price);
            $this->assertEquals(40, $stock->cost_price);
            $this->assertEquals(75, $stock->yard_selling_price);
            $this->assertEquals(40, $stock->yard_cost_price);
        } finally {
            DB::rollBack();
        }
    }
}
