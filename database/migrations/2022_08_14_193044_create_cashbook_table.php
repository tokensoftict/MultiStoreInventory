<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashbookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashbook', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('last_updated')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount',20,5);
            $table->date('transaction_date');
            $table->string('type')->default('Credit'); //Credit and Debit
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->string('comment')->nullable();
            $table->timestamps();

            $table->foreign('last_updated')->on('users')->references('id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cashbook');
    }
}
