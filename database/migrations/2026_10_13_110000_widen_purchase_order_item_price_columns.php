<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Widen purchase order item prices (decimal(8,2) -> decimal(20,5), still nullable) so per-piece
 * prices derived from unit prices (e.g. 1000 / 3) are not truncated. Non-destructive widening.
 */
class WidenPurchaseOrderItemPriceColumns extends Migration
{
    public function up()
    {
        if (Schema::hasTable('purchase_order_items')) {
            DB::statement('ALTER TABLE purchase_order_items MODIFY cost_price DECIMAL(20,5) NULL, MODIFY selling_price DECIMAL(20,5) NULL');
        }
    }

    public function down()
    {
        // intentionally left as is: narrowing could truncate data
    }
}
