<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositPaymentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposit_payment_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId("payment_id")->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId("payment_method_id")->nullable();
            $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("customer_id")->nullable()->constrained()->nullOnDelete();
            $table->string("deposit_number")->nullable();
            $table->foreignId("deposit_id")->nullable()->constrained()->nullOnDelete();
            $table->decimal("amount",20,5);
            $table->date("payment_date");
            $table->timestamps();

            $table->foreign('payment_method_id')->references('id')->on('payment_method_table')
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
        Schema::dropIfExists('deposit_payment_logs');
    }
}
