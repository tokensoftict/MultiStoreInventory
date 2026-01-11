<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string("deposit_number",255)->unique();
            $table->string("deposit_paper_number",255)->unique();
            $table->string('department')->default('STORE');
            $table->foreignId('warehousestore_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string("discount_type")->nullable(); //['Fixed','Percentage','None']
            $table->decimal("discount_amount",8,2)->nullable();
            $table->string("status")->default("DRAFT"); //["PAID","DRAFT","DISCOUNT","VOID","HOLD","COMPLETE"]
            $table->decimal("sub_total",20,5);
            $table->decimal("total_amount_paid",20,5);
            $table->decimal("total_profit",20,5);
            $table->decimal("total_cost",20,5);
            $table->decimal("vat",20,5);
            $table->decimal("vat_amount",20,5);
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("last_updated_by")->nullable();
            $table->date("deposit_date");
            $table->time("deposit_time");
            $table->timestamps();

            $table->foreign('last_updated_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposits');
    }
}
