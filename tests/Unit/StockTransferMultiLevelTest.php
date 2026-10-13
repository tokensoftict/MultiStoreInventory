<?php

namespace Tests\Unit;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehousestore;
use Tests\TestCase;

class StockTransferMultiLevelTest extends TestCase
{
    public function test_stock_transfer_item_fillable_and_casts()
    {
        $item = new StockTransferItem([
            'stock_id' => 1,
            'user_id' => 1,
            'product_type' => 'yard_quantity',
            'from' => 1,
            'to' => 2,
            'selling_price' => 100.0,
            'cost_price' => 80.0,
            'quantity' => 50,
            'unit_id' => 3,
            'unit_name' => 'Carton',
            'unit_quantity' => 2,
            'unit_price' => 2500.0,
            'base_quantity' => 50,
            'transfer_date' => '2026-10-13',
        ]);

        $this->assertEquals(3, $item->unit_id);
        $this->assertEquals('Carton', $item->unit_name);
        $this->assertEquals(2.0, $item->unit_quantity);
        $this->assertEquals(2500.0, $item->unit_price);
        $this->assertEquals(50.0, $item->base_quantity);
        $this->assertEquals(50, $item->quantity);
    }

    public function test_get_actual_store_helper_with_multi_level_store()
    {
        // Multi-level store always resolves to its yard_column (base pieces)
        $store = new Warehousestore();
        $store->id = 999;
        $store->quantity_system = 'multi_level';
        $store->packed_column = 'quantity';
        $store->yard_column = 'yard_quantity';

        // When store is multi-level, inventory is in yard_column
        $col = \App\Services\InventoryQuantityService::usesMultiLevel($store)
            ? $store->yard_column
            : $store->packed_column;

        $this->assertEquals('yard_quantity', $col);
    }

    public function test_dashboard_get_near_out_of_stock_items_supports_multi_level()
    {
        $dashboard = app(\App\Classes\Dashboard::class);
        $items = $dashboard->getNearOutOfStockItems();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $items);
        if ($items->count() > 0) {
            $first = $items->first();
            $this->assertTrue(property_exists($first, 'name'));
            $this->assertTrue(property_exists($first, 'available_quantity'));
            $this->assertTrue(property_exists($first, 'reorder_level'));
            if (!empty($first->is_multi)) {
                $this->assertTrue(property_exists($first, 'breakdown_label'));
                $this->assertTrue(property_exists($first, 'reorder_label'));
            }
        }
    }

    public function test_stale_session_store_refreshes_quantity_system()
    {
        // When session store is missing quantity_system, usesMultiLevel and getActiveStore reload from DB
        $store = Warehousestore::find(1);
        if ($store) {
            $stale = (object)[
                'id' => $store->id,
                'name' => $store->name,
                'packed_column' => $store->packed_column,
                'yard_column' => $store->yard_column,
            ];
            session()->put('activeStore', (array)$stale);

            $this->assertTrue(\App\Services\InventoryQuantityService::usesMultiLevel($stale));
            $active = getActiveStore();
            $this->assertEquals('multi_level', $active->quantity_system);
        }
    }
}
