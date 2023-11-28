<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('from');
            $table->unsignedBigInteger('to');
            $table->decimal('total_price',20,5);
            $table->string('status')->default('DRAFT');
            $table->string('type')->nullable();
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
