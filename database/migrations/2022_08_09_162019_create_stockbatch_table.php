<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockbatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stockbatches', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->date("received_date")->nullable();
            $table->date("expiry_date")->nullable();
            $table->bigInteger("quantity")->default(0);
            $table->bigInteger("yard_quantity")->default(0);
            $table->unsignedBigInteger("supplier_id")->nullable();
            $table->foreignId("stock_id")->nullable()->constrained()->nullOnDelete();
            $table->foreign("supplier_id")->references("id")->on("supplier")->nullOnDelete();
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
        Schema::dropIfExists('stockbatches');
    }
}
