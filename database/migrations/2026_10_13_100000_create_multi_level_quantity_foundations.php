<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive-only foundations for the optional Carton -> Packet -> Piece system.
 * Nothing existing is renamed, dropped or rewritten. Every existing store stays 'legacy'.
 */
class CreateMultiLevelQuantityFoundations extends Migration
{
    /** Transaction tables that get nullable unit snapshot columns. */
    private $snapshotTables = [
        'invoice_items',
        'invoice_item_batches',
        'purchase_order_items',
        'stock_transfer_items',
        'return_logs',
    ];

    public function up()
    {
        if (!Schema::hasColumn('warehousestores', 'quantity_system')) {
            Schema::table('warehousestores', function (Blueprint $table) {
                $table->string('quantity_system', 20)->default('legacy');
            });
        }

        if (!Schema::hasTable('product_units')) {
            Schema::create('product_units', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('stock_id')->index();
                $table->string('name', 100);
                $table->unsignedTinyInteger('level'); // 1 carton, 2 packet, 3 piece
                $table->unsignedBigInteger('parent_unit_id')->nullable(); // next LARGER unit
                $table->decimal('units_per_parent', 20, 4)->nullable(); // how many of THIS unit in one parent
                $table->decimal('cost_price', 20, 4)->default(0);
                $table->decimal('selling_price', 20, 4)->default(0);
                $table->boolean('status')->default(true);
                $table->timestamps();

                $table->unique(['stock_id', 'level']);
            });
        }

        foreach ($this->snapshotTables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'unit_id')) {
                    $table->unsignedBigInteger('unit_id')->nullable();
                }
                if (!Schema::hasColumn($tableName, 'unit_name')) {
                    $table->string('unit_name', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'unit_quantity')) {
                    $table->decimal('unit_quantity', 20, 4)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'unit_price')) {
                    $table->decimal('unit_price', 20, 4)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'base_quantity')) {
                    $table->decimal('base_quantity', 20, 4)->nullable();
                }
            });
        }
    }

    public function down()
    {
        foreach ($this->snapshotTables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }
            foreach (['unit_id', 'unit_name', 'unit_quantity', 'unit_price', 'base_quantity'] as $col) {
                if (Schema::hasColumn($tableName, $col)) {
                    Schema::table($tableName, function (Blueprint $table) use ($col) {
                        $table->dropColumn($col);
                    });
                }
            }
        }

        Schema::dropIfExists('product_units');

        if (Schema::hasColumn('warehousestores', 'quantity_system')) {
            Schema::table('warehousestores', function (Blueprint $table) {
                $table->dropColumn('quantity_system');
            });
        }
    }
}
