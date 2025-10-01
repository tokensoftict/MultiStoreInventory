<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncentivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incentives', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehousestore_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal("amount",20,5);
            $table->time("payment_time")->nullable();
            $table->date("payment_date")->nullable();
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
        Schema::dropIfExists('incentives');
    }
}
