<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->mediumText('name')->nullable();
            $table->text("description")->nullable();
            $table->string("code",255)->nullable();
            $table->unsignedBigInteger('product_category_id')->nullable();
            $table->foreignId('manufacturer_id')->nullable()->constrained()->onDelete("set null");
            $table->decimal("selling_price",20,5)->nullable();
            $table->decimal('yard_selling_price',20,5)->default(0);
            $table->decimal("cost_price",20,5)->nullable();
            $table->decimal('yard_cost_price',20,5)->default(0);
            $table->text("barcode")->nullable();
            $table->string("location",255)->nullable();
            $table->string('type')->default("NORMAL"); // NORMAl & PIECES Pieces
            $table->boolean("expiry")->default("0");
            $table->boolean("status")->default("1");
            $table->string("image")->nullable();
            $table->unsignedBigInteger("user_id")->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->timestamps();

            $table->foreign("user_id")
                ->references("id")->on('users')->onDelete("set null");

            $table->foreign("last_updated_by")
                ->references("id")->on('users')->onDelete("set null");

            $table->foreign('product_category_id')->references("id")->on('product_category')
                ->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
