<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceNumberToStockLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_log_items', function (Blueprint $table) {
            $table->string("invoice_number")->nullable()->after('product_type');
        });

        Schema::table('stock_log_operations', function (Blueprint $table) {
            $table->string("invoice_number")->nullable()->after('store');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_log_items', function (Blueprint $table) {
            $table->dropColumn('invoice_number');
        });

        Schema::table('stock_log_operations', function (Blueprint $table) {
            $table->dropColumn('invoice_number');
        });
    }
}
