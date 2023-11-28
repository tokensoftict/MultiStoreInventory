<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTakingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_taking_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('stock_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('available_quantity')->nullable();
            $table->bigInteger('available_yard_quantity')->nullable();
            $table->bigInteger('counted_available_quantity')->nullable();
            $table->bigInteger('counted_yard_quantity')->nullable();
            $table->bigInteger('available_quantity_diff');
            $table->bigInteger('available_yard_quantity_diff');
            $table->foreignId('stock_taking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehousestore_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('Pending');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_taking_items');
    }
}
