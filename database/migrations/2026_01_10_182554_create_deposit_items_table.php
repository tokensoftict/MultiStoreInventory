<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposit_items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId("deposit_id")->constrained()->cascadeOnDelete();
            $table->foreignId("stock_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehousestore_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('department')->default('STORE');
            $table->bigInteger("quantity");
            $table->foreignId("customer_id")->nullable()->constrained()->nullOnDelete();
            $table->string("status")->default("DRAFT"); //["PAID","DRAFT","DISCOUNT","VOID","HOLD","COMPLETE"]
            $table->unsignedBigInteger("added_by");
            $table->date("deposit_date");
            $table->string("store")->default('quantity');
            $table->time("deposit_time");
            $table->decimal("cost_price",20,5);
            $table->decimal("selling_price",20,5);
            $table->decimal("profit",20,5);
            $table->decimal("total_cost_price",20,5);
            $table->decimal("total_selling_price",20,5);
            $table->decimal("total_profit",20,5);
            $table->string("discount_type")->nullable(); //['Fixed','Percentage','None']
            $table->decimal("discount_amount",20,5)->nullable();
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
        Schema::dropIfExists('deposit_items');
    }
}
