<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockLogUasageTypeToStockLogItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_log_items', function (Blueprint $table) {
            $table->foreignId('stock_log_usages_type_id')->nullable()->after('usage_type')->constrained()->nullOnDelete();
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
            $table->dropConstrainedForeignId('stock_log_usages_type_id');
        });
    }
}
