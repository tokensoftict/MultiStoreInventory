<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreSeprationRelatedModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->foreignId('warehousestore_id')->after('code')->nullable()->default("1")->constrained()->cascadeOnDelete();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('warehousestore_id')->after('phone_number')->nullable()->default("1")->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign('warehousestore_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign('warehousestore_id');
        });
    }
}
