<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string("invoice_number",255)->unique();
            $table->string("invoice_paper_number",255)->unique();
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
            $table->unsignedBigInteger("voided_by")->nullable();
            $table->date("invoice_date");
            $table->time("sales_time");
            $table->mediumText("void_reason")->nullable();
            $table->date("date_voided")->nullable();
            $table->time("void_time")->nullable();
            $table->timestamps();

            $table->foreign('last_updated_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('voided_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_invoices');
    }
}
