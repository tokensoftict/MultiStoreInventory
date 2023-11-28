<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierCreditPaymentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_credit_payment_history', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger("supplier_id")->nullable();
            $table->foreignId("purchase_order_id")->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger("payment_method_id")->nullable();
            $table->string('payment_info')->nullable();
            $table->decimal("amount",20,5);
            $table->date("payment_date");
            $table->timestamps();
            $table->foreign('payment_method_id')->references('id')->on('payment_method')->nullOnDelete();
            $table->foreign('supplier_id')->references('id')->on('supplier')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_credit_payment_history');
    }
}
