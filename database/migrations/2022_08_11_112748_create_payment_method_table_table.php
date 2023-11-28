<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_method_table', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("customer_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("payment_id")->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('warehousestore_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('department')->default('STORE');
            $table->unsignedBigInteger("payment_method_id")->nullable();
            $table->morphs('invoice');
            $table->date("payment_date");
            $table->decimal("amount",20,5);
            $table->text("payment_info")->nullable();
            $table->timestamps();

            $table->foreign('payment_method_id')->references('id')->on('payment_method')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_method_table');
    }
}
