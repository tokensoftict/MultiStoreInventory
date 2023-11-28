<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->engine = 'InnoDB';
            $table->foreignId("purchase_order_id")->constrained()->cascadeOnDelete();
            $table->foreignId("stock_id")->constrained()->cascadeOnDelete();
            $table->foreignId('stockbatch_id')->nullable()->constrained()->nullOnDelete();
            $table->date("expiry_date")->nullable();
            $table->string("store")->nullable();
            $table->integer("qty")->default("0");
            $table->decimal("cost_price",8,2)->nullable();
            $table->decimal("selling_price",8,2)->nullable();
            $table->unsignedBigInteger("added_by")->nullable();
            $table->timestamps();
            $table->foreign('added_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
    }
}
