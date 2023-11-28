<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDebitPaymentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_payment_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId("payment_id")->constrained()->cascadeOnDelete();
            $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("payment_method_id");
            $table->foreignId("customer_id")->nullable()->constrained()->nullOnDelete();
            $table->string("invoice_number")->nullable();
            $table->foreignId("invoice_id")->nullable()->constrained()->nullOnDelete();
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
        Schema::dropIfExists('credit_payment_logs');
    }
}
