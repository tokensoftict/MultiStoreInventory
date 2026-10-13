<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddMultiLevelUnitColumnsToDepositItems extends Migration
{
    public function up()
    {
        if (Schema::hasTable('deposit_items')) {
            Schema::table('deposit_items', function (Blueprint $table) {
                if (!Schema::hasColumn('deposit_items', 'unit_id')) {
                    $table->unsignedBigInteger('unit_id')->nullable()->after('store');
                }
                if (!Schema::hasColumn('deposit_items', 'unit_name')) {
                    $table->string('unit_name', 100)->nullable()->after('unit_id');
                }
                if (!Schema::hasColumn('deposit_items', 'unit_quantity')) {
                    $table->decimal('unit_quantity', 20, 4)->nullable()->after('unit_name');
                }
                if (!Schema::hasColumn('deposit_items', 'unit_price')) {
                    $table->decimal('unit_price', 20, 4)->nullable()->after('unit_quantity');
                }
                if (!Schema::hasColumn('deposit_items', 'base_quantity')) {
                    $table->decimal('base_quantity', 20, 4)->nullable()->after('unit_price');
                }
            });

            // Widen quantity column to decimal(20,4) so fractional/converted pieces are not truncated
            DB::statement("ALTER TABLE `deposit_items` MODIFY `quantity` DECIMAL(20,4) NOT NULL DEFAULT 0");
        }
    }

    public function down()
    {
        if (Schema::hasTable('deposit_items')) {
            Schema::table('deposit_items', function (Blueprint $table) {
                foreach (['unit_id', 'unit_name', 'unit_quantity', 'unit_price', 'base_quantity'] as $col) {
                    if (Schema::hasColumn('deposit_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
}
