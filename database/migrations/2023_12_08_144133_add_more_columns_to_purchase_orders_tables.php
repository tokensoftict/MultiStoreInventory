<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreColumnsToPurchaseOrdersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
             $table->string('purchase_order_invoice_number', 100)->nullable()->after('supplier_id');
             $table->enum('type', ['PURCHASE', 'RETURN'])->default('PURCHASE')->after('supplier_id');
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->string('purchase_order_invoice_number', 100)->nullable()->after('stockbatch_id');
            $table->enum('type', ['PURCHASE', 'RETURN'])->default('PURCHASE')->after('stockbatch_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['purchase_order_invoice_number', 'type']);
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['purchase_order_invoice_number', 'type']);
        });
    }
}
