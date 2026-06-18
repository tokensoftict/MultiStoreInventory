<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDynamicPricingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('price_type', ['packed', 'yard']);
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->foreignId('price_category_id')->constrained('price_categories')->onDelete('cascade');
            $table->decimal('price', 15, 4);
            $table->timestamps();

            $table->unique(['stock_id', 'price_category_id']);
        });

        Schema::create('price_categories_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_category_id')->constrained('price_categories')->onDelete('cascade');
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->decimal('old_price', 15, 4)->nullable();
            $table->decimal('new_price', 15, 4);
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('price_category_id')->nullable()->constrained('price_categories')->onDelete('set null');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('price_category_id')->nullable()->constrained('price_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['price_category_id']);
            $table->dropColumn('price_category_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['price_category_id']);
            $table->dropColumn('price_category_id');
        });

        Schema::dropIfExists('price_categories_history');
        Schema::dropIfExists('stock_prices');
        Schema::dropIfExists('price_categories');
    }
}
