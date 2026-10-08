<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeInvoiceQuantityColumnsToDecimal extends Migration
{
    /**
     * Static columns: table => [columns]
     */
    private function staticColumns(): array
    {
        return [
            'invoice_items'        => ['quantity'],
            'invoice_item_batches' => ['quantity'],
            'return_logs'          => ['quantity_before', 'quantity_after', 'dif'],
            'stockbatches'         => ['quantity', 'yard_quantity'],
        ];
    }

    /**
     * Per-store dynamic columns (packed_xxx / yard_xxx) living on stockbatches.
     */
    private function dynamicStockbatchColumns(): array
    {
        if (!Schema::hasTable('warehousestores')) {
            return [];
        }

        $columns = [];
        foreach (DB::table('warehousestores')->get(['packed_column', 'yard_column']) as $store) {
            foreach ([$store->packed_column, $store->yard_column] as $col) {
                if ($col && Schema::hasColumn('stockbatches', $col)) {
                    $columns[] = $col;
                }
            }
        }
        return $columns;
    }

    public function up()
    {
        foreach ($this->staticColumns() as $table => $columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` DECIMAL(20,4) NOT NULL DEFAULT 0");
                }
            }
        }

        foreach ($this->dynamicStockbatchColumns() as $column) {
            DB::statement("ALTER TABLE `stockbatches` MODIFY `{$column}` DECIMAL(20,4) NOT NULL DEFAULT 0");
        }
    }

    public function down()
    {
        foreach ($this->staticColumns() as $table => $columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` BIGINT NOT NULL DEFAULT 0");
                }
            }
        }

        foreach ($this->dynamicStockbatchColumns() as $column) {
            DB::statement("ALTER TABLE `stockbatches` MODIFY `{$column}` BIGINT NOT NULL DEFAULT 0");
        }
    }
}
