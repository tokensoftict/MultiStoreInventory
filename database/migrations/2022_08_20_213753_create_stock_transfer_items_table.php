<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransferItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_transfer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_type')->nullable();
            $table->unsignedBigInteger('from');
            $table->unsignedBigInteger('to');
            $table->decimal("selling_price",8,2);
            $table->decimal('cost_price',8,2);
            $table->bigInteger('quantity');
            $table->date('transfer_date');
            $table->timestamps();
            $table->foreign('from')
                ->on('warehousestores')
                ->references('id')
                ->cascadeOnDelete();
            $table->foreign('to')
                ->on('warehousestores')
                ->references('id')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_transfers');
    }
}
