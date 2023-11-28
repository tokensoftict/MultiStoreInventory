<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_logs', function (Blueprint $table) {
            $table->id();
            $table->string('store_after')->nullable();
            $table->foreignId('stock_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehousestore_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string("invoice_number",255)->nullable();
            $table->string("invoice_paper_number",255)->nullable();
            $table->date('date_added');
            $table->bigInteger('quantity_before')->default(0);
            $table->bigInteger('quantity_after')->default(0);
            $table->decimal('selling_price',20,5);
            $table->bigInteger('dif')->default(0);
            $table->string('store_before')->nullable();
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
        Schema::dropIfExists('return_logs');
    }
}
